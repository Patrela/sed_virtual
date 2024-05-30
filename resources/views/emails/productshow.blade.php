<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SED Product Email</title>
    <link rel="stylesheet" href="{{ asset('css/ppal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <div id="product_mail" class="modal_mail">
        <form id="mailDetailForm" class="modal-container" method="get" action="#" class="p-6">
            @csrf
            <div class="modal-card-title" id="prod_name">{{ $product['name'] }}</div>
            <div class="filter-container-two-column">
                <div class="modal-card-body">
                    <div class="quantity" id="prod_stock">{{ $product['stock_quantity'] }}</div>
                    <div class="modal-card-image">
                        <img id="prod_img_1" src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                    </div>
                </div>
                <div class="modal-card-body">
                    <div class="modal-card-image">
                        <img id="prod_img_2" src="{{ $product['image_2'] }}" alt="">
                    </div>
                </div>
            </div>

            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Sku</div>
                    <div class="modal-card-item" id="prod_sku">{{ $product['sku'] }}</div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-division">
                        <div class="modal-card-item-title">$</div>
                        <div class="modal-card-item" id="prod_price">{{ $product['regular_price'] }}</div>
                        <div class="modal-card-item" id="prod_currency">{{ $product['currency'] }}</div>
                    </div>
                    <div class="modal-card-item-division">
                        <div class="modal-card-item-title">/</div>
                        <div class="modal-card-item" id="prod_unit">{{ $product['unit'] }}</div>
                        <div class="modal-card-item" id="prod_tax_status">{{ $product['price_tax_status'] }}</div>
                    </div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Stock</div>
                    <div class="modal-card-item" id="prod_stock_1">{{ $product['stock_quantity'] }}</div>
                </div>
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Marca</div>
                    <div class="modal-card-item" id="prod_brand">{{ $product['brand'] }}</div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title"> Clasificación </div>
                    <div class="modal-card-item" id="prod_department">{{ $product['department'] }}</div> *
                    <div class="modal-card-item" id="prod_category">{{ $product['category'] }}</div> *
                    <div class="modal-card-item" id="prod_segment">{{ $product['segment'] }}</div>
                </div>
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Peso</div>
                    <div class="modal-card-item" id="dimension_weight">{{ $product['dimension_weight'] }}</div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title"> Dimensiones Largo*Alto*Ancho </div>

                    <div class="modal-card-item" id="dimension_length">{{ $product['dimension_length'] }}</div> *
                    <div class="modal-card-item" id="dimension_width">{{ $product['dimension_width'] }}</div> *
                    <div class="modal-card-item" id="dimension_height"> {{ $product['dimension_height'] }}</div>
                </div>
            </div>
            <div class="modal-card-text" id="prod_description">{{ $product['description'] }}</div>
            <div class="modal-card-text" id="prod_attributes">{{ htmlentities(str_replace("\r\n", ' | ', $product['attributes'])) }}</div>
            <div class="modal-card-text" id="prod_guarantee">{{ $product['guarantee'] }}
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Gerente de Producto</div>
                    <div class="modal-card-item" id="prod_contact">{{ $product['contact_agent'] }}</div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">División</div>
                    <div class="modal-card-item" id="prod_contact_unit">{{ $product['contact_unit'] }}</div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

