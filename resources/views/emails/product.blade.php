<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mail Product</title>
    <style type="text/css">
    h1 { font-size:1.5em; font-weight:bolder;color: darkslateblue;}
    a{ background-color:darkblue; color: aqua; padding: 15px 25px; text-decoration: none;}
    .modal-card-image {
        width: 310px;
        margin: 0.25em;
        padding: 0.5rem;
        border: 1px solid black;
        height: 310px;
        padding: 0.75rem;
    }
  .modal-card-image img {
    margin:0;
    padding: 1px;
    width: 298px;
    height: 298px;
  }
  .modal-card-item {
    padding:  0.25rem;
  }
  .modal-card-item-title {
    padding: 0.25rem;
    font-weight: bold;
  }
  .modal-card-item-division {
    display: flex;
    flex-direction: row;
    gap: 0.5rem;
  }
  .modal-card-text {
    padding: 0.25em 0.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: normal;
    min-height: 0.75rem;
}
    </style>
</head>
<body>
    <h3>Contacto: {{ $sender }}</h3>
    <br />
    <h1>REF. {{ $product['sku'] }} : {{ $product['name'] }}</h1>
    <h2>STOCK= {{ $product['stock_quantity'] }} {{ $product['unit'] }}  MARCA= {{ $product['brand'] }}</h2>
    <div class="modal-card-image">
        <img id="prod_img_1" src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
    </div>
    <div class="modal-card-item-division">
            <div class="modal-card-item-title">$</div>
            <div class="modal-card-item" id="prod_price">{{ $product['regular_price'] }}</div>
            <div class="modal-card-item" id="prod_currency">{{ $product['currency'] }}</div>
            <div class="modal-card-item" id="prod_tax_status">{{ $product['price_tax_status'] }}</div>
    </div>
    <div class="modal-card-text" id="prod_description">{{ $product['description'] }}</div>
    <div class="modal-card-text" id="prod_attributes">{{ $product['attributes'] }}</div>
    <div class="modal-card-item-title">Garantía</div>
    <div class="modal-card-text" id="prod_guarantee">{{ $product['guarantee'] }}
    <br/>
    <h3> Esperamos su comunicación, para resolver toda inquietud comercial.</h3>
    <br/>
    <h3>Información enviada referente a precios mayorista de SED INTERNATIONAL</h3>

</body>
</html>

