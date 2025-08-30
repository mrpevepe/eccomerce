@extends('layouts.main')
@section('title', $product->name)
@section('content')
    <div class="product-details-container">
        <!-- Carrossel de Imagens -->
        <div class="carousel">
            <div class="carousel-inner">
                @foreach ($allImages as $index => $image)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" data-variation-id="{{ $image instanceof \App\Models\ProductVariationImage ? $image->variation_id : '' }}">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="Product Image">
                    </div>
                @endforeach
            </div>
            <button class="carousel-btn prev" onclick="changeSlide(-1)">&#10094;</button>
            <button class="carousel-btn next" onclick="changeSlide(1)">&#10095;</button>
            
            <!-- Thumbnails das imagens -->
            <div class="carousel-thumbnails">
                @foreach ($allImages as $index => $image)
                    <div class="thumbnail {{ $index == 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="Thumbnail {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Informacoes do Produto -->
        <div class="product-info">
            <h1>{{ $product->name }}</h1>
            <p class="price">R${{ number_format($product->price, 2) }}</p>
            <p class="reviews">4.5mil avaliações (placeholder)</p>

            <!-- Variações -->
            <h3>Variações</h3>
            <div class="variations">
                @foreach ($product->variations as $variation)
                    <button class="variation-btn {{ $loop->first ? 'active' : '' }}" data-variation-id="{{ $variation->id }}" onclick="selectVariation({{ $variation->id }})">
                        {{ $variation->name }}: {{ $variation->value }} (+R${{ number_format($variation->additional_price, 2) }})
                    </button>
                @endforeach
            </div>

            <!-- Quantidade -->
            <div class="quantity">
                <h3>Quantidade</h3>
                <button class="decrement" onclick="changeQuantity(-1)">-</button>
                <input type="number" value="1" min="1" class="quantity-input" id="quantity">
                <button class="increment" onclick="changeQuantity(1)">+</button>
            </div>

            <div class="actions">
                <button class="add-to-cart">Adicionar ao Carrinho</button>
                <button class="buy-now">Comprar Agora</button>
            </div>
        </div>
    </div>

    <!-- Categoria e Descrição -->
    <div class="product-description">
        <h3>Categoria: {{ $product->category->name }}</h3>
        <p>{{ $product->description }}</p>
    </div>

    <!-- Avaliações (Placeholder) -->
    <div class="reviews-section">
        <h3>Avaliações</h3>
        <p>Aqui vão as avaliações do produto (placeholder).</p>
    </div>

<script>
let currentSlide = 0;
const totalSlides = {{ count($allImages) }};

function changeSlide(direction) {
    document.querySelectorAll('.carousel-item')[currentSlide].classList.remove('active');
    document.querySelectorAll('.thumbnail')[currentSlide].classList.remove('active');
    
    currentSlide += direction;
    
    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }
    
    document.querySelectorAll('.carousel-item')[currentSlide].classList.add('active');
    document.querySelectorAll('.thumbnail')[currentSlide].classList.add('active');
}

function goToSlide(index) {
    document.querySelectorAll('.carousel-item')[currentSlide].classList.remove('active');
    document.querySelectorAll('.thumbnail')[currentSlide].classList.remove('active');
    
    currentSlide = index;
    
    document.querySelectorAll('.carousel-item')[currentSlide].classList.add('active');
    document.querySelectorAll('.thumbnail')[currentSlide].classList.add('active');
}

function selectVariation(variationId) {
    document.querySelectorAll('.variation-btn').forEach(btn => btn.classList.remove('active'));
    
    document.querySelector(`[data-variation-id="${variationId}"]`).classList.add('active');
    
    const variationImages = document.querySelectorAll('.carousel-item');
    for (let i = 0; i < variationImages.length; i++) {
        if (variationImages[i].getAttribute('data-variation-id') == variationId) {
            goToSlide(i);
            break;
        }
    }
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let currentQuantity = parseInt(quantityInput.value);
    let newQuantity = currentQuantity + change;
    
    if (newQuantity >= 1) {
        quantityInput.value = newQuantity;
    }
}

// Auto-play carousel
/*
setInterval(() => {
    changeSlide(1);
}, 5000); // Muda a cada 5 segundos
*/

document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        changeSlide(-1);
    } else if (e.key === 'ArrowRight') {
        changeSlide(1);
    }
});

let startX = 0;
let endX = 0;

document.querySelector('.carousel').addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

document.querySelector('.carousel').addEventListener('touchend', function(e) {
    endX = e.changedTouches[0].clientX;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diffX = startX - endX;
    
    if (Math.abs(diffX) > swipeThreshold) {
        if (diffX > 0) {
            changeSlide(1); // prox slide
        } else {
            changeSlide(-1); // slide anterior
        }
    }
}
</script>

<style>
.product-details-container {
    display: flex;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.carousel {
    width: 500px;
}

.carousel-inner {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.carousel-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.carousel-item.active {
    opacity: 1;
}

.carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 18px;
    border-radius: 4px;
    z-index: 10;
}

.carousel-btn:hover {
    background: rgba(0,0,0,0.8);
}

.prev {
    left: 10px;
}

.next {
    right: 10px;
}

.carousel-thumbnails {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 5px 0;
}

.thumbnail {
    width: 80px;
    height: 80px;
    border: 2px solid transparent;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    flex-shrink: 0;
    transition: border-color 0.3s ease;
}

.thumbnail:hover {
    border-color: #ddd;
}

.thumbnail.active {
    border-color: #3498db;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    flex: 1;
}

.product-info h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 1rem;
}

.variations {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 1rem 0;
}

.variation-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.variation-btn.active {
    border-color: #3498db;
    background: #3498db;
    color: white;
}

.quantity {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 1rem 0;
}

.decrement, .increment {
    width: 40px;
    height: 40px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    border-radius: 4px;
}

.quantity-input {
    width: 60px;
    height: 40px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.add-to-cart, .buy-now {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}

.add-to-cart {
    background: #2ecc71;
    color: white;
}

.buy-now {
    background: #e74c3c;
    color: white;
}

.product-description, .reviews-section {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}

@media (max-width: 768px) {
    .product-details-container {
        flex-direction: column;
        padding: 1rem;
    }
    
    .carousel {
        width: 100%;
    }
    
    .carousel-inner {
        height: 300px;
    }
}
</style>
@endsection