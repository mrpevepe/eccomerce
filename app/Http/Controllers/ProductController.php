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
        $product = Product::with(['images', 'variations.image'])->findOrFail($id);
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
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            'delete_variation_images' => 'nullable|array',
            'delete_variation_images.*' => 'exists:product_variation_images,id',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.value' => 'required_with:variations|string|max:100',
            'variations.*.additional_price' => 'required_with:variations|numeric|min:0',
            'variations.*.stock_quantity' => 'required_with:variations|integer|min:0',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::findOrFail($imageId);
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        if ($request->hasFile('main_image')) {
            $existingMain = $product->images()->where('is_main', true)->first();
            if ($existingMain) {
                Storage::disk('public')->delete($existingMain->path);
                $existingMain->delete();
            }
            $path = $request->file('main_image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        if ($request->has('additional_images')) {
            $currentAdditionalCount = $product->images()->where('is_main', false)->count();
            $newAdditionalImages = array_filter($request->file('additional_images') ?? [], function($file) {
                return $file && $file->isValid();
            });
            if ($currentAdditionalCount + count($newAdditionalImages) > 3) {
                return back()->withErrors(['additional_images' => 'You can add up to 3 additional images.']);
            }
            foreach ($newAdditionalImages as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_main' => false,
                ]);
            }
        }

        if ($request->has('delete_variation_images')) {
            foreach ($request->delete_variation_images as $imageId) {
                $image = ProductVariationImage::findOrFail($imageId);
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        if ($request->has('variations')) {
            $sentVariationIds = collect($request->variations)
                ->filter(fn($v) => isset($v['id']))
                ->pluck('id')
                ->all();

            $variationsToDelete = $product->variations()->whereNotIn('id', $sentVariationIds)->get();
            foreach ($variationsToDelete as $variationToDelete) {
                if ($variationToDelete->image) {
                    Storage::disk('public')->delete($variationToDelete->image->path);
                    $variationToDelete->image->delete();
                }
                $variationToDelete->delete();
            }

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
        $product = Product::with('variations')->findOrFail($id);
        return view('admin.products.variations', compact('product'));
    }
}