<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>
    <div class="table-row" id="productContainer">

        @foreach ($products as $key => $product)
            @if ($key >= $start && $key < $end)

                <div class="card">
                    <div class="card-body">
                        <div class="quantity">
                            {{ number_format($product['stock_quantity'], 0, ",", ".") }}
                        </div>
                        <h6 class="card-title">{{ $product['name'] }} - {{ $product['category'] }}</h6>
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

</x-app-layout>
