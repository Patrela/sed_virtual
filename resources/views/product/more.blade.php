<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>
    <div class="table_row" id="productContainer">
        @php
            $counter = 0;
            $perPage = 18;
            //$page = session()->has('numberPage') ? session('numberPage') : 1;
            $page = request()->has('page') ? request()->page : 1;
            $start = ($page - 1) * $perPage;
            $end = ($start + $perPage) > $total ? $total : $start + $perPage;
        @endphp
        @foreach ($products as $key => $product)
            @if ($key >= $start && $key < $end)
                <div class="card">
                    <div class="body_card">
                        <div class="quantity">
                            {{ number_format($product['stock_quantity'], 0, ",", ".") }}
                        </div>
                        <h6 class="card_title">{{ $product['name'] }}</h6>
                        <div class="card_image">
                            <img src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                        </div>
                    </div>
                    <div class="body_text">
                        <p class="card_text">{{ $product['sku'] }}</p>
                        <p class="card_text">{{ '$ ' . number_format($product['regular_price'], 2, ",", ".") }}</p>
                    </div>
                </div>
                @php
                    $counter++;
                @endphp
            @endif
        @endforeach
    </div>
    <div id="more">
        <button id="loadMoreBtn" class="btn btn-primary mt-4">Ver Más</button>
        <input type="hidden" id="page" value="{{ $page }}">
        <input type="hidden" id="perPage" value="{{ $perPage }}">
        <input type="hidden" id="total" value="{{ $total }}">
        <input type="hidden" id="counter" value="{{ $counter }}">
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var loadMoreBtn = document.getElementById('loadMoreBtn');
            loadMoreBtn.addEventListener('click', function() {
                var productContainer = document.getElementById('productContainer');
                var currentPage = parseInt(document.getElementById('page').value);
                var perPage = parseInt(document.getElementById('perPage').value);
                var total = parseInt(document.getElementById('total').value);
                var counter = parseInt(document.getElementById('counter').value);

                var nextPage = currentPage + 1;
                var start = (nextPage - 1) * perPage;
                var end = Math.min(start + perPage, total);

                // Mostrar más productos si hay más disponibles
                @foreach ($products as $key => $product)
                    if ({{$key}} >= start && {{$key}} < end) {
                        var card = document.createElement('div');
                        card.className = 'card';
                        var bodyCard = document.createElement('div');
                        bodyCard.className = 'body_card';
                        var quantity = document.createElement('div');
                        quantity.className = 'quantity';
                        quantity.textContent = '{{ number_format($product['stock_quantity'], 0, ",", ".") }}';
                        var cardTitle = document.createElement('h6');
                        cardTitle.className = 'card_title';
                        cardTitle.textContent = '{{ $product['name'] }}';
                        var cardImage = document.createElement('div');
                        cardImage.className = 'card_image';
                        var image = document.createElement('img');
                        image.src = '{{ $product['image_1'] }}';
                        image.alt = '{{ $product['name'] }}';
                        cardImage.appendChild(image);
                        bodyCard.appendChild(quantity);
                        bodyCard.appendChild(cardTitle);
                        bodyCard.appendChild(cardImage);
                        card.appendChild(bodyCard);
                        var bodyText = document.createElement('div');
                        bodyText.className = 'body_text';
                        var cardText1 = document.createElement('p');
                        cardText1.className = 'card_text';
                        cardText1.textContent = '{{ $product['sku'] }}';
                        var cardText2 = document.createElement('p');
                        cardText2.className = 'card_text';
                        cardText2.textContent = '{{ '$ ' . number_format($product['regular_price'], 2, ",", ".") }}';
                        bodyText.appendChild(cardText1);
                        bodyText.appendChild(cardText2);
                        card.appendChild(bodyText);
                        productContainer.appendChild(card);
                    }
                @endforeach

                // Actualizar valores de la paginación
                document.getElementById('page').value = nextPage;
                document.getElementById('counter').value = counter + end - start;
                if (counter + end - start >= total) {
                    loadMoreBtn.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
