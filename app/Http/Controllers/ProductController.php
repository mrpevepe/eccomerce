<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\ProductVariationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'images')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images' => 'nullable|array|max:3',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.value' => 'required_with:variations|string|max:100',
            'variations.*.additional_price' => 'required_with:variations|numeric|min:0',
            'variations.*.stock_quantity' => 'required_with:variations|integer|min:0',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                if ($image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_main' => false,
                    ]);
                }
            }
        }

        if ($request->has('variations')) {
            foreach ($request->variations as $index => $variationData) {
                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'name' => $variationData['name'],
                    'value' => $variationData['value'],
                    'additional_price' => $variationData['additional_price'],
                    'stock_quantity' => $variationData['stock_quantity'],
                ]);

                if ($request->hasFile("variations.$index.image")) {
                    $path = $request->file("variations.$index.image")->store('products', 'public');
                    ProductVariationImage::create([
                        'variation_id' => $variation->id,
                        'path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::with('variations.image', 'images')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images' => 'nullable|array|max:3',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|exists:product_variations,id',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.value' => 'required_with:variations|string|max:100',
            'variations.*.additional_price' => 'required_with:variations|numeric|min:0',
            'variations.*.stock_quantity' => 'required_with:variations|integer|min:0',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        // Handle main image
        if ($request->hasFile('main_image')) {
            $mainImage = $product->images->where('is_main', true)->first();
            if ($mainImage) {
                Storage::disk('public')->delete($mainImage->path);
                $mainImage->delete();
            }
            $path = $request->file('main_image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        // Handle additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                if ($image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_main' => false,
                    ]);
                }
            }
        }

        // Delete selected images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::findOrFail($imageId);
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Handle variations
        if ($request->has('variations')) {
            foreach ($request->variations as $index => $variationData) {
                if (isset($variationData['id'])) {
                    $variation = ProductVariation::findOrFail($variationData['id']);
                    $variation->update([
                        'name' => $variationData['name'],
                        'value' => $variationData['value'],
                        'additional_price' => $variationData['additional_price'],
                        'stock_quantity' => $variationData['stock_quantity'],
                    ]);
                } else {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'name' => $variationData['name'],
                        'value' => $variationData['value'],
                        'additional_price' => $variationData['additional_price'],
                        'stock_quantity' => $variationData['stock_quantity'],
                    ]);
                }

                if ($request->hasFile("variations.$index.image")) {
                    if ($variation->image) {
                        Storage::disk('public')->delete($variation->image->path);
                        $variation->image->delete();
                    }
                    $path = $request->file("variations.$index.image")->store('products', 'public');
                    ProductVariationImage::create([
                        'variation_id' => $variation->id,
                        'path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        // Delete product images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        // Delete variation images
        foreach ($product->variations as $variation) {
            if ($variation->image) {
                Storage::disk('public')->delete($variation->image->path);
                $variation->image->delete();
            }
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function showStockForm($id)
    {
        $product = Product::with('variations')->findOrFail($id);
        return view('admin.products.stock', compact('product'));
    }

    public function updateStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'variations' => 'required|array',
            'variations.*.id' => 'required|exists:product_variations,id',
            'variations.*.stock_quantity' => 'required|integer|min:0',
        ]);

        foreach ($request->variations as $variationData) {
            $variation = ProductVariation::findOrFail($variationData['id']);
            $variation->update(['stock_quantity' => $variationData['stock_quantity']]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Stock updated successfully.');
    }

    public function deactivate($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'inactive']);
        return redirect()->route('admin.products.index')->with('success', 'Product deactivated successfully.');
    }

    public function showVariations($id)
    {
        $product = Product::with(['variations', 'images'])->findOrFail($id);
        return view('admin.products.variations', compact('product'));
    }

    public function showVariationStockForm($productId, $variationId)
    {
        $variation = ProductVariation::with('product')->findOrFail($variationId);
        if ($variation->product_id !== (int)$productId) {
            abort(404); // garante que a variation pertence ao produto
        }
        return view('admin.products.variation_stock', compact('variation'));
    }

    public function updateVariationStock(Request $request, $productId, $variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        if ($variation->product_id !== (int)$productId) {
            abort(404);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $variation->update(['stock_quantity' => $request->stock_quantity]);

        return redirect()->route('admin.products.variations', $productId)->with('success', 'Estoque da variação atualizado com sucesso.');
    }

    public function indexHome()
    {
        $categories = Category::take(10)->get(); // 10 categorias por enquanto alterar talvez...
        $products = Product::with('images')->where('status', 'active')->take(36)->get(); // 36 produtos
        return view('welcome', compact('categories', 'products'));
    }

    public function show($id)
    {
        $product = Product::with(['images', 'variations.image', 'category'])->findOrFail($id);
        if ($product->status !== 'active') {
            abort(404); // apenas produtos ativos
        }

        // ardena as imagens > main primeiro, depois adicionais, depois as de variations
        $mainImage = $product->images->where('is_main', true)->first();
        $additionalImages = $product->images->where('is_main', false);
        $variationImages = $product->variations->map(function ($variation) {
            return $variation->image;
        })->filter();

        $allImages = collect();
        if ($mainImage) {
            $allImages->push($mainImage);
        }
        $allImages = $allImages->merge($additionalImages)->merge($variationImages);

        return view('products.show', compact('product', 'allImages'));
    }
}