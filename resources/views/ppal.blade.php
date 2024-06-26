<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/ppal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $maingroupName = $maingroupName ?? '';
        function WithoutQuotation($text)
        {
            $text = str_replace("'", '´', $text);
            return str_replace('"', '´', $text);
        }
    @endphp
    <script type="text/javascript">
        var products = @json($products);
        let allProducts = products; // Global variable to store all products
    </script>
</head>

<body>
    <x-mainmenu />
    <main> <!--class="mt-6" -->
        <aside>
            <div class="aside-container" name="main_group" id="main_group">
                <input type="hidden" name="current-group" id="current-group" value="{{ $maingroup }}" />
                <input type="hidden" name="current-group-name" id="current-group-name" value="{{ $maingroupName }}" />
                <input type="hidden" id="selected-brands" name="selected-brands" value="">
                <input type="hidden" id="selected-brands-name" name="selected-brands-name" value="">
                <input type="hidden" id="selected-categories" name="selected-categories" value="">
                <input type="hidden" id="selected-categories-name" name="selected-categories-name" value="">

                <div class="title">GRUPOS</div>
                <div class="button-container">
                    @foreach ($departments as $key => $department)
                        <button
                            class="department-button {{ $maingroup == $department['id'] ? 'aside-button-active' : '' }}"
                            value="{{ $department['id'] }}" id="dep-{{ $department['id'] }}" {{-- onclick="departmentActions(this)" --}}
                            onclick="departmentRoute('{{ $department['name'] }}')"
                            name="dep-{{ $department['id'] }}">{{ $department['name'] }}</button>
                        @if ($department['id'] == $maingroup)
                            @php
                                $maingroupName = $department['name'];
                            @endphp
                            <script type="text/javascript">
                                const GroupTitle = document.getElementById('current-group-name');
                                GroupTitle.value = "<?php echo $department['name']; ?>";
                                //alert(NameInput.value);
                                //const currentButton = document.getElementById("dep-<?php echo $department['id']; ?>");
                                //departmentActions(currentButton);
                            </script>
                        @endif
                    @endforeach
                </div>
                {{-- <button>Más &nbsp;&nbsp;<i class="mx-3 fas fa-caret-down"></i></a></button> --}}
            </div>
            <p></p>
            <div class="aside-container filter">
                @if ($searchText === '')
                    <h3 class="title">FILTROS</h3>
                    <button>Categoría</button>
                    <div id="category_detail" name="category_detail" class="filter-container-two-column">
                        @foreach ($categories as $key => $category)
                            <div class="filter-checkbox">
                                <div class="filter-checkbox-input">
                                    <input id="cat-{{ $category['id'] }}" name="cat-array"
                                        value="{{ $category['name'] }}" type="checkbox" {{-- onclick="addCategoryToList(this, '{{ $category['name'] }}','{{ $maingroupName}}')" --}}>

                                </div>
                                <div class="filter-checkbox-label">
                                    {{ $category['name'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button>Marca</button>
                    <div id="brand_detail" name="brand_detail" class="filter-container">
                        @foreach ($brands as $key => $brand)
                            <div class="filter-checkbox">
                                <div class="filter-checkbox-input">
                                    <input id="brand-{{ $brand['id'] }}" type="checkbox" name="brand-array"
                                        value="{{ $brand['name'] }}" {{-- onclick="addBrandToList(this, '{{ $brand['name'] }}','{{ $maingroupName}}')" --}}>
                                </div>
                                <div class="filter-checkbox-label">
                                    {{ $brand['name'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <h3 class="title">ACTUALIZAR</h3>
                <div class="filter-container-one-column">
                    <div class="centered">Última actualización</div>
                    <div class="centered">
                        <strong>{{ session()->has('lastUpdated') ? session('lastUpdated') : date('d/m/Y H:i:s') }}</strong>
                    </div>
                    <div class="centered">
                        <form action="{{ route('refresh') }}" method="GET">
                            @csrf
                            <button type="submit">Cargar Ahora</button>
                        </form>
                    </div>
                    {{-- <div class="centered">
                        <form action="#" method="GET">
                            @csrf
                            <button type="button" onclick="vtexLogin('a1@correo.com', 'Other One')">Vtex User</button>
                        </form>
                    </div> --}}
                </div>
            </div>

        </aside>
        <section class="main-section" id="main-section">
            <div class="product-list">
                <div>
                    <h2 class="product-title" id="product-title" name="product-title">
                        @if ($searchText === '')
                            {{ $maingroupName }}
                        @else
                            {{ $searchText }}
                        @endif
                    </h2>
                </div>
                <div class="product-order">
                    <span class="product-list-title">Ordenar por</span>
                    <select name="order-options" id="order-options"> {{-- onchange="OrderSelection()" --}}
                        <option value="">Seleccionar</option>
                        <option value="price-plus">Mayor a Menor Precio</option>
                        <option value="price-less">Menor a Mayor Precio</option>
                        <option value="stock-plus">Mayor a Menor Stock</option>
                        <option value="stock-less">Menor a Mayor Stock</option>
                        <option value="brands">Marca</option>
                    </select>
                </div>
            </div>
            @php
                $counter = 0;
                $perPage = request()->has('perPage') ? request()->perPage : 300; //18;
                //$page = session()->has('numberPage') ? session('numberPage') : 1;
                $page = request()->has('page') ? request()->page : 1;
                $total = request()->has('total') ? request()->total : count($products); //
                $start = ($page - 1) * $perPage;
                $end = $start + $perPage > $total ? $total : $start + $perPage;
                //dd($products);
                //dd("pag. " .$page . "= " .$perPage );
            @endphp
            <div class="table-row" id="products-container">
                @forelse ($products as $key => $product)
                    {{-- @foreach ($products as $key => $product) --}}
                    @if ($key >= $start && $key < $end)
                        <div class="card">
                            <div class="card-body">
                                <div class="quantity">
                                    {{ number_format($product['stock_quantity'], 0, ',', '.') }}
                                </div>
                                <h6 class="card-title">{{ $product['name'] }} </h6>
                                <div class="card-image">
                                    <img src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                                </div>
                            </div>
                            <div class="card-body-text">
                                <div class="card-text">
                                    {{-- <button onclick="ModalDetail('{{ $product['name'] }}', '{{ $product['sku'] }}',
                                    {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }},
                                    '{{ $product['image_1'] }}', '{{ $product['image_2'] }}', '{{ $product['image_3'] }}', '{{ $product['image_4'] }}',
                                    '{{ $product['currency'] }}', '{{ $product['description'] }}', '{{ $product['unit'] }}',
                                    '{{ $product['department'] }}', '{{ $product['category'] }}', '{{ $product['brand'] }}', '{{ $product['segment'] }}',
                                    '{{ htmlentities(str_replace("\r\n", '<br>', $product['attributes'])) }}',
                                    '{{ $product['guarantee'] }}', '{{ $product['contact_agent'] }}', '{{ $product['contact_unit'] }}',
                                    {{ $product['dimension_length'] }}, {{ $product['dimension_width'] }}, {{ $product['dimension_height'] }},{{ $product['dimension_weight'] }}
                                    )">
                                        {{ $product['sku'] }} / {{ $product['brand'] }}
                                    </button> --}}

                                    <button
                                        onclick="ModalDetail(cleanQuotation('{{ $product['name'] }}'),
                                        cleanQuotation('{{ $product['sku'] }}'),
                                        {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }}, '{{ $product['price_tax_status'] }}',
                                        '{{ $product['image_1'] }}', '{{ $product['image_2'] }}', '{{ $product['image_3'] }}', '{{ $product['image_4'] }}',
                                        '{{ $product['currency'] }}',
                                        cleanQuotation('{{ $product['description'] }}'),
                                        '{{ $product['unit'] }}',
                                        '{{ $product['department'] }}',
                                        cleanQuotation('{{ $product['category'] }}'),
                                        '{{ $product['brand'] }}', '{{ $product['segment'] }}',
                                        cleanQuotation('{{ htmlentities(str_replace("\r\n", ' | ', $product['attributes'])) }}'),
                                        '{{ $product['guarantee'] }}', '{{ $product['contact_agent'] }}', '{{ $product['contact_unit'] }}',
                                        {{ $product['dimension_length'] }}, {{ $product['dimension_width'] }}, {{ $product['dimension_height'] }},{{ $product['dimension_weight'] }}
                                    )">
                                        {{ $product['sku'] }} / {{ $product['brand'] }}
                                        {{-- {{ Illuminate\Support\Facades\Log::info($product['attributes']) }} --}}
                                    </button>
                                    <button class="navitem" type="button"
                                        onclick="productMail('{{ $product['sku'] }}')">
                                        <i class="fas fa-envelope navitem-icon"></i>
                                    </button>
                                    <button class="navitem" type="button"
                                        onclick="productFormCSV('{{ WithoutQuotation($product['name']) }}',
                                        '{{ WithoutQuotation($product['sku']) }}',
                                        {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }}, '{{ $product['price_tax_status'] }}',
                                        '{{ $product['image_1'] }}', '{{ $product['image_2'] }}', '{{ $product['image_3'] }}', '{{ $product['image_4'] }}',
                                        '{{ $product['currency'] }}',
                                        '{{ WithoutQuotation($product['description']) }}',
                                        '{{ $product['unit'] }}',
                                        '{{ $product['department'] }}',
                                        '{{ WithoutQuotation($product['category']) }}',
                                        '{{ $product['brand'] }}', '{{ $product['segment'] }}',
                                        '{{ htmlentities(str_replace("\r\n", ' | ', WithoutQuotation($product['attributes']))) }}',
                                        '{{ $product['guarantee'] }}', '{{ $product['contact_agent'] }}', '{{ $product['contact_unit'] }}',
                                        {{ $product['dimension_length'] }}, {{ $product['dimension_width'] }}, {{ $product['dimension_height'] }},{{ $product['dimension_weight'] }}
                                    )">
                                        <i class="fas fa-file-csv navitem-icon"></i>
                                    </button>
                                </div>
                                <div class="card-text">
                                    {{ '$ ' . number_format($product['regular_price'], 2, ',', '.') }}
                                    {{ $product['currency'] }} / {{ $product['unit'] }}
                                    {{ $product['price_tax_status'] }}
                                </div>
                            </div>
                        </div>
                        @php
                            $counter++;
                        @endphp
                    @endif
                @empty
                    <h1> No hay productos para la selección</h1>
                @endforelse

                {{-- @foreach ($products as $key => $product)
                    @if ($key >= $start && $key < $end)
                        <div class="card">
                            <div class="card-body">
                                <div class="quantity">
                                    {{ number_format($product['stock_quantity'], 0, ',', '.') }}
                                </div>
                                <h6 class="card-title">{{ $product['name'] }} </h6>
                                <div class="card-image">
                                    <img src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                                </div>
                            </div>
                            <div class="card-body-text">
                                <div class="card-text">
                                    <button onclick="ModalDetail('{{ $product['name'] }}', '{{ $product['sku'] }}',
                                    {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }}, {{ $product['price_tax_status'] }}
                                    '{{ $product['image_1'] }}', '{{ $product['image_2'] }}', '{{ $product['image_3'] }}', '{{ $product['image_4'] }}',
                                    '{{ $product['currency'] }}', '{{ $product['description'] }}', '{{ $product['unit'] }}',
                                    '{{ $product['department'] }}', '{{ $product['category'] }}', '{{ $product['brand'] }}', '{{ $product['segment'] }}',
                                    '{{ htmlentities(str_replace("\r\n", '<br>', $product['attributes'])) }}',
                                    '{{ $product['guarantee'] }}', '{{ $product['contact_agent'] }}', '{{ $product['contact_unit'] }}',
                                    {{ $product['dimension_length'] }}, {{ $product['dimension_width'] }}, {{ $product['dimension_height'] }},{{ $product['dimension_weight'] }}
                                    )">
                                        {{ $product['sku'] }} / {{ $product['brand'] }}
                                    </button>
                                </div>
                                <div class="card-text">
                                    {{ '$ ' . number_format($product['regular_price'], 2, ',', '.') }}
                                    {{ $product['currency'] }}  / {{ $product['unit'] }} {{ $product['price_tax_status'] }}
                                </div>
                            </div>
                        </div>
                        @php
                            $counter++;
                        @endphp
                    @endif
                @endforeach --}}
            </div>
        </section>

    </main>
    <footer>
        <p class="footer-top"> Recuerde refrescar la página para un inventario actualizado.</p>
        <div class="footer-medium">
            <div class="footer-medium-left">
                <p class="footer-title">Documentación</p>
                <p>Documentación técnica para API de Consulta de Inventarios</p>
            </div>
            <div class="footer-medium-right">
                <form action="{{ route('postman.stock') }}" method="POST">
                    @csrf
                    <button type="submit">API Postman</button>
                </form>
            </div>
        </div>
        <p class="footer-bottom">El contenido de este sitio, incluyendo textos, imágenes y código, es propiedad de SED
            INTERNATIONAL DE COLOMBIA S.A.S., y está protegido por las leyes internacionales de derecho de autor.</p>
    </footer>


    <div id="window-csv" class="modal">

        <form id="form-csv" class="modal-container" method="get" action="#" class="p-6">
            @csrf
            <h1 class="footer-title">CSV - Copiar a Excel TEXTO EN COLUMNA: Delimitado (,) Texto(")</h1>
            <div id="product_csv_desc" name="product_csv_desc" class="modal-card-item-container">
                <div class="modal-card-item" id="prod_csv_desc"></div>
            </div>

            <h3 class="modal-card-item-title">CSV y encabezado</h3>
            <div id="product_csv" name="product_csv" class="modal-card-item-container">
                {{-- <textarea class="modal-card-item" id="prod_csv" cols="30" rows="10"> --}}
                <div class="modal-card-item" id="prod_csv"></div>
            </div>
            <h3 class="modal-card-item-title">CSV datos</h3>
            <div id="product_csv_noheader" name="product_csv_noheader" class="modal-card-item-container">
                <div class="modal-card-item" id="prod_csv_noheader"></div>
            </div>
            <div class="filter-container-one-column centered distance-top">
                <div class="modal-card-item-division">
                    <button onclick="closeModal('window-csv')">Cerrar</button>
                </div>
            </div>
        </form>
    </div>

    <div id="window-detail" class="modal">
        <form id="skuDetailForm" class="modal-container" method="get" action="#" class="p-6">
            @csrf
            <div class="modal-card-title" id="prod_name"></div>
            <div class="modal-box-img">
                <div class="modal-full-img">
                    <img class="modal-full-img" id="prod_full_img" src="" alt="">
                </div>
                <div class="modal-thumb-bar">
                    <img id="prod_img_1" src="" alt="" onclick="changeFullImage(this)">
                    <img id="prod_img_2" src="" alt="" onclick="changeFullImage(this)">
                    <img id="prod_img_3" src="" alt="" onclick="changeFullImage(this)">
                    <img id="prod_img_4" src="" alt="" onclick="changeFullImage(this)">
                </div>
            </div>
            {{-- <div class="filter-container-two-column">
                <div class="modal-card-body">
                    <div class="quantity" id="prod_stock"></div>
                    <div class="modal-card-image">
                        <img id="prod_img_1" src="" alt="">
                    </div>
                </div>
                <div class="modal-card-body">
                    <div class="modal-card-image">
                        <img id="prod_img_2" src="" alt="">
                    </div>
                </div>
                <div class="modal-card-body modal-card-hide">
                    <div class="modal-card-image">
                        <img id="prod_img_3" src="" alt="">
                    </div>
                </div>
                <div class="modal-card-body modal-card-hide">
                    <div class="modal-card-image">
                        <img id="prod_img_4" src="" alt="">
                    </div>
                </div>
            </div> --}}
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Sku</div>
                    <div class="modal-card-item" id="prod_sku"></div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-division">
                        <div class="modal-card-item-title">$</div>
                        <div class="modal-card-item" id="prod_price"></div>
                        <div class="modal-card-item" id="prod_currency"></div>
                    </div>
                    <div class="modal-card-item-division">
                        <div class="modal-card-item-title">/</div>
                        <div class="modal-card-item" id="prod_unit"></div>
                        <div class="modal-card-item" id="prod_tax_status"></div>
                    </div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Stock</div>
                    <div class="modal-card-item" id="prod_stock_1"></div>
                </div>
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Marca</div>
                    <div class="modal-card-item" id="prod_brand"></div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title"> Clasificación </div>
                    <div class="modal-card-item" id="prod_department"></div> *
                    <div class="modal-card-item" id="prod_category"></div> *
                    <div class="modal-card-item" id="prod_segment"></div>
                </div>
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Peso</div>
                    <div class="modal-card-item" id="dimension_weight"></div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title"> Dimensiones Largo*Alto*Ancho </div>
                    <div class="modal-card-item" id="dimension_length"></div> *
                    <div class="modal-card-item" id="dimension_width"></div> *
                    <div class="modal-card-item" id="dimension_height"></div>
                </div>
            </div>
            <div class="modal-card-text" id="prod_description">
            </div>
            <div class="modal-card-text" id="prod_attributes">
            </div>
            <div class="modal-card-text" id="prod_guarantee">
            </div>
            <div class="modal-card-item-container">
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">Gerente de Producto</div>
                    <div class="modal-card-item" id="prod_contact"></div>
                </div>
                <div class="modal-card-item-division">
                    <div class="modal-card-item-title">División</div>
                    <div class="modal-card-item" id="prod_contact_unit"></div>
                </div>
            </div>
            {{-- <div id="product_csv" name="product_csv"  class="modal-card-item-container invisible">
                    <div class="modal-card-item-title">CSV</div>
                    <div class="modal-card-item" id="prod_csv"></div>
            </div> --}}
            <div class="filter-container-one-column centered distance-top">
                <div class="modal-card-item-division">
                    {{-- <button id="prod_email" name="prod_email" onclick="productMail('')">email</button> --}}
                    {{-- <button id="btn_prod_csv" name="btn_prod_csv" onclick="productCSV()">CSV</button> --}}
                    <button onclick="closeModal('window-detail')">Cerrar</button>
                </div>
            </div>

        </form>
    </div>
</body>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', (event) => {
        const categoryCheckboxes = document.querySelectorAll('input[name="cat-array"]');
        const brandCheckboxes = document.querySelectorAll('input[name="brand-array"]');
        const orderSelect = document.getElementById('order-options');
        const searchText = document.getElementById('search');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => updateProductsDisplay());
        });

        brandCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => updateProductsDisplay());
        });

        orderSelect.addEventListener('change', () => updateProductsDisplay());
        searchText.addEventListener('keyup', function(event) {
            //alert(event.key);
            if (event.key === 'Enter') { // || event.key === 'Tab'
                searchWilcardProduct();
            }
        });
        searchText.addEventListener('change', function(event) {
            //alert(event.key);
            searchWilcardProduct();
        });
    });
    /**
     * activates the search route with the search text
     */
    function searchWilcardProduct() {
        const searchText = document.getElementById('search').value;
        if (searchText !== '') window.location.href = "{{ route('search', ['searchText' => ':searchText']) }}".replace(
            ':searchText', searchText);
    }

    function departmentRoute(groupName) {
        temporalIndicator();
        const productTitle = document.getElementById('product-title');
        productTitle.textContent = groupName;
        //window.location.href = '/products/' + groupName;
        window.location.href = "{{ route('product.show', ['group' => ':group']) }}".replace(':group', groupName);
    }

    function productMail(sku) {
        if (sku === "") {
            sku = document.getElementById("prod_sku").textContent;
        }

        let receiver = prompt("Correo del destinatario:");
        if (receiver.indexOf("@") !== -1) {
            const urlpath = "{{ route('product.email', ['sku' => ':sku']) }}".replace(":sku", sku);
            console.log(urlpath);

            fetch(urlpath, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "x-api-receiver": receiver,
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    //resolve(data);
                    //if (data["code"] === 200) alert("correo enviado");
                    //if (data["code"] === 404) alert(data["result"]);
                    console.log(data);
                })
                .catch((error) => {
                    // Check for 404 response specifically
                    if (error.response && error.response.status === 404) {
                        alert("Product not found! Could not send email.");
                        console.error("Product not found:", error.response.data); // Log error details for debugging
                    } else {
                        console.error("Error fetching mail:", error); // Log other errors
                    }
                });
        }
    }

    function vtexLogin(usermail, username = "New User") {
        const element = document.getElementById("products-container");
        element.action = "";

        const urlpath = "{{ route('vtex.conection', ['username' => ':username']) }}".replace(":username", username);

        fetch(urlpath, {
                method: "GET",
                headers: {
                    "Authorization": "Bearer HsMaBkQPI1ReVNN1ppIZ9KXTeSTSJNJ9VNMY4U2bSxi2y",
                    "Content-Type": "application/json",
                    "x-api-user": usermail,
                },
            })
            .then((response) =>(response.status==200)? response.text() : response.json())
            .then((data) => {
                if (data.hasOwnProperty("code")) {
                    element.innerHTML = ` <strong>Code:</strong> ${data.code} <br>
                                        <strong>Message:</strong> ${data.message || "No message provided"}`;
                } else {
                    //console.error(data);
                    //element.innerHTML = data;
                    window.location.href = "{{ route('ppal') }}";
                }
            })
            .catch((error) => {
                if (error.response) {
                    console.error("Error:", error.response.status, error.response.statusText);
                    element.innerText = `Error: ${error.response.statusText}`;
                } else {
                    console.error("Error fetching data:", error);
                    element.innerText = "Error fetching data";
                }
            });
    }

</script>

<script type="text/javascript" src="{{ asset('js/viewFunctions.js') }}"></script>

</html>
