<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>
    <div class="table-row" id="productContainer">
        @php
            $counter = 0;
            $perPage =  request()->has('perPage') ? request()->perPage : 4; //18;
            //$page = session()->has('numberPage') ? session('numberPage') : 1;
            $page = request()->has('page') ? request()->page : 1;
            $start = ($page - 1) * $perPage;
            $end = ($start + $perPage) > $total ? $total : $start + $perPage;
            //dd($products);
            //dd("pag. " .$page . "= " .$perPage );
        @endphp
        @foreach ($products as $key => $product)
            @if ($key >= $start && $key < $end)

                <div class="card">
                    <div class="card-body">
                        <div class="quantity">
                            {{ number_format($product['stock_quantity'], 0, ",", ".") }}
                        </div>
                        <h6 class="card-title">{{ $product['name'] }} - {{ $product['stock_quantity'] }}</h6>
                        <div class="card-image" >
                            <img src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                        </div>
                    </div>
                    <div class="card-body-text"
                    onclick="openSkuDetailModal('{{ $product['name'] }}', '{{ $product['sku'] }}', '{{ $product['stock_quantity'] }}', '{{ $product['regular_price'] }}', '{{ $product['image_1'] }}')"
                    >
                        <p class="card-text">{{ $product['sku'] }}</p>
                        <p class="card-text">{{ '$ ' . number_format($product['regular_price'], 2, ",", ".") }}</p>
                    </div>
                </div>
                @php
                    $counter++;
                @endphp
            @endif
        @endforeach
    </div>
    <div id="more">
        <button id="loadMoreBtn">Ver Más {{ $total }}</button>
        <button id="moreBtn" class="main-button">Ver Más {{ $total }}</button>
        <input type="hidden" id="page" value="{{ $page }}">
        <input type="hidden" id="perPage" value="{{ $perPage }}">
        <input type="hidden" id="total" value="{{ $total }}">
        <input type="hidden" id="counter" value="{{ $counter }}">
    </div>

    <x-modal id="skuDetail" name="skuDetail" :show focusable>
        <form id="skuDetailForm" method="post" action="#" class="p-6">
            <h2 id='product_modal_name' class="text-lg font-medium text-gray-900">
            </h2>

            <p id ='product_modal_sku' class="mt-1 text-sm text-gray-600">
            </p>
            <div class="mt-6">
                <x-input-label for="product_modal_quantity" value="Stock" class="sr-only" />

                <x-text-input
                    id="product_modal_quantity"
                    name="product_modal_quantity"
                    type="text"
                    class="mt-1 block w-3/4"
                    disabled
                />
            </div>
            <div class="mt-6">
                <x-input-label for="product_modal_price" value="Price" class="sr-only" />
                <x-text-input
                    id="product_modal_price"
                    name="product_modal_price"
                    type="text"
                    class="mt-1 block w-3/4"
                    disabled
                />
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="quantity" id="product_modal_quantity_2">
                    </div>
                    <h6 class="card-title" id="product_modal_name_2"></h6>
                    <div class="card-image" >
                        <img id="product_modal_image_2" src="" alt="">
                    </div>
                </div>
                <div class="card-body-text">
                    <p class="card-text" id="product_modal_sku_2"></p>
                    <p class="card-text" id="product_modal_price_2"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cerrar') }}
                </x-secondary-button>

            </div>
        </form>
    </x-modal>

    <script>

        function openSkuDetailModal(name, sku, quantity, regularPrice, productImage) {
            // Mostrar el modal skuDetail
            console.log(name);
            //document.querySelector('[name="skuDetail"]').dispatchEvent(new CustomEvent('show'));
            document.getElementById("skuDetail").dispatchEvent(new CustomEvent('show'));
            // Actualizar los elementos del modal con los valores recibidos

            document.getElementById("product_modal_name").innerText = name;
            document.getElementById("product_modal_name_2").innerText = name;
            document.getElementById("product_modal_sku").innerText = sku;
            document.getElementById("product_modal_sku_2").innerText = sku;
            document.getElementById("product_modal_quantity").innerText = quantity;
            document.getElementById("product_modal_quantity_2").innerText = quantity;
            document.getElementById("product_modal_price").innerText = regularPrice;
            document.getElementById("product_modal_price_2").innerText = regularPrice;
            document.getElementById("product_modal_image_2").src = productImage;
            //$('#skuDetail').modal('show');

        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("skuDetailForm").addEventListener("submit", function(event) {
                event.preventDefault(); // Evitar el envío del formulario
                // Cerrar el modal
                document.querySelector('[name="skuDetail"]').dispatchEvent(new CustomEvent('close'));
            });
        });
    </script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("moreBtn").addEventListener("click", function() {
            alert("moreBtn clicked");
            var productContainer = document.getElementById('productContainer');
            var currentPage = parseInt(document.getElementById('page').value);
            var perPage = parseInt(document.getElementById('perPage').value);
            var total = parseInt(document.getElementById('total').value);
            var counter = parseInt(document.getElementById('counter').value);

            var nextPage = currentPage + 1;
            var start = (nextPage - 1) * perPage;
            var end = Math.min(start + perPage, total);
            console.log( " start " + start + " end " + end);
            //console.log( {{$products[0]['name']}} );
            // @foreach ($products as $key => $product)
            //     if ({{$key}} >= start && {{$key}} < end) {
            //         console.log( {{$product['part_num']}});
            // @endforeach

            // Actualizar valores de la paginación
            document.getElementById('page').value = nextPage;
            document.getElementById('counter').value = counter + end - start;
            if (counter + end - start >= total) {
                loadMoreBtn.style.display = 'none';
            }


        });
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var loadMoreBtn = document.getElementById('loadMoreBtn');
        loadMoreBtn.addEventListener('click', function() {
            console.log( " clic yeah ");
            var productContainer = document.getElementById('productContainer');
            var currentPage = parseInt(document.getElementById('page').value);
            var perPage = parseInt(document.getElementById('perPage').value);
            var total = parseInt(document.getElementById('total').value);
            var counter = parseInt(document.getElementById('counter').value);

            var nextPage = currentPage + 1;
            var start = (nextPage - 1) * perPage;
            var end = Math.min(start + perPage, total);
            console.log( " start " + start + " end " + end);
            // Mostrar más productos si hay más disponibles
            @foreach ($products as $key => $product)
                if ({{$key}} >= start && {{$key}} < end) {
                    console.log( {{$product['part_num']}});
                    var card = document.createElement('div');
                    card.className = 'card';
                    var bodyCard = document.createElement('div');
                    bodyCard.className = 'card-body';
                    var quantity = document.createElement('div');
                    quantity.className = 'quantity';
                    quantity.textContent = '{{ number_format($product['stock_quantity'], 0, ",", ".") }}';
                    var cardTitle = document.createElement('h6');
                    cardTitle.className = 'card-title';
                    cardTitle.textContent = '{{ $product['name'] }}';
                    var cardImage = document.createElement('div');
                    cardImage.className = 'card-image';
                    var image = document.createElement('img');
                    image.src = '{{ $product['image_1'] }}';
                    image.alt = '{{ $product['name'] }}';
                    cardImage.appendChild(image);
                    bodyCard.appendChild(quantity);
                    bodyCard.appendChild(cardTitle);
                    bodyCard.appendChild(cardImage);
                    card.appendChild(bodyCard);
                    var bodyText = document.createElement('div');
                    bodyText.className = 'card-body-text';
                    bodyText.addEventListener('click', openSkuDetailModal('{{ $product['name'] }}', '{{ $product['sku'] }}',
                        '{{ number_format($product['stock_quantity'], 0, ",", ".") }}',
                        '{{ '$ ' . number_format($product['regular_price'], 2, ",", ".") }}' ,
                        '{{ $product['image_1'] }}' ));
                    var cardText1 = document.createElement('p');
                    cardText1.className = 'card-text';
                    cardText1.textContent = '{{ $product['sku'] }}';
                    var cardText2 = document.createElement('p');
                    cardText2.className = 'card-text';
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
