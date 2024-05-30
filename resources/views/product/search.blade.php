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

            {{-- <button id="btn-1" name="btn-test" type="button" onclick="fetchAbilities('Saly')">abilities</button>
            <button id="btn-2" name="btn-2" type="button" onclick="productMail('30FN0031LM')">30FN0031LM</button> --}}


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
                <h3 class="title">LIMPIAR</h3>
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
                </div>
            </div>

        </aside>
        <section class="main-section" id="main-section">
            <div class="product-list">
                <div>
                    <h2 class="product-title" id="product-title" name="product-title">{{ $searchText }}
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
                                        onclick="ModalDetail('{{ WithoutQuotation($product['name']) }}',
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
        <form id="form-detail" class="modal-container" method="get" action="#" class="p-6">
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
    function filterProducts(products, selectedCategories, selectedBrands) {
        return products.filter(product => {
            const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(product
                .category);
            const brandMatch = selectedBrands.length === 0 || selectedBrands.includes(product.brand);
            return categoryMatch && brandMatch;
        });
    }

    function sortAndFilterProducts(products, criteria, selectedCategories, selectedBrands) {
        // First filter the products
        let filteredProducts = filterProducts(products, selectedCategories, selectedBrands);

        // Then sort the filtered products
        switch (criteria) {
            case 'price-plus':
                filteredProducts.sort((a, b) => b.regular_price - a.regular_price);
                break;
            case 'price-less':
                filteredProducts.sort((a, b) => a.regular_price - b.regular_price);
                break;
            case 'stock-plus':
                filteredProducts.sort((a, b) => b.stock_quantity - a.stock_quantity);
                break;
            case 'stock-less':
                filteredProducts.sort((a, b) => a.stock_quantity - b.stock_quantity);
                break;
            case 'brands':
                filteredProducts.sort((a, b) => a.brand.localeCompare(b.brand));
                break;
        }
        return filteredProducts;
    }

    function getSelectedFilters() {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="cat-array"]:checked')).map(cb => cb
            .value);
        const selectedBrands = Array.from(document.querySelectorAll('input[name="brand-array"]:checked')).map(cb => cb
            .value);
        const orderCriteria = document.getElementById('order-options').value;

        return {
            selectedCategories,
            selectedBrands,
            orderCriteria
        };
    }

    function updateProductsDisplay() {

        const {
            selectedCategories,
            selectedBrands,
            orderCriteria
        } = getSelectedFilters();
        // update the title of filters
        const groupElement = document.getElementById('current-group-name');
        const title = groupElement.value;
        let categories = "";
        let brands = "";
        if (selectedCategories.length > 0) {
            categories = ` ${selectedCategories.join('-')}`;
        }
        if (selectedBrands.length > 0) {
            brands = ` ${selectedBrands.join('-')}`;
        }
        //alert('Update Products ' + title + ' '+ categories + ' ' + brands );
        const titleElement = document.getElementById('product-title');
        titleElement.textContent = `${title}${categories}${brands}`;

        const filteredAndSortedProducts = sortAndFilterProducts(allProducts, orderCriteria, selectedCategories,
            selectedBrands);
        generateCards(filteredAndSortedProducts);
    }

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

    // Si el usuario hace click en la x, la ventana se cierra
    function closeModal(windowForm) {
        var modal = document.getElementById(`${windowForm}`);
        modal.style.display = "none";
    };

    //formating numbers
    function intlRound(number, decimals = 2) {
        if (number === null || number === undefined || number === '') {
            return ''; // Return empty string for invalid input
        }
        const options = {
            minimumFractionDigits: decimals, // Ensure at least 'decimals' digits after decimal point
            maximumFractionDigits: decimals, // Limit to 'decimals' digits after decimal point
        };

        const formatter = new Intl.NumberFormat('en-US',
            options); // Use US English locale for formatting without commas
        return formatter.format(number);
    }

    //validate strings
    function isEmpty(str) {
        return (!str || str.trim().length == 0);
    }
    //replace all cases, including end of line
    function replaceModelString(input, origin, replacement) {
        var output = input;
        if (isEmpty(input)) return "";
        while (output.includes(origin)) {
            output = output.replace(origin, replacement);
        }
        return output;
    }


    function productFormCSV(prod_name, prod_sku, prod_stock, prod_price, prod_tax_status, prod_img_1, prod_img_2,
        prod_img_3, prod_img_4,
        prod_currency, prod_description, prod_unit,
        prod_department, prod_category, prod_brand, prod_segment, prod_attributes,
        prod_guarantee, prod_contact, prod_contact_unit,
        dimension_length, dimension_width, dimension_height, dimension_weight
    ) {

        var modal = document.getElementById("window-csv");
        const description = document.getElementById("prod_csv_desc");
        // Replace `<br>` tags
        description.innerHTML = replaceModelString(decodeURIComponent(prod_attributes), "&lt;br&gt;", " | ");;

        var csvFormat = document.getElementById("prod_csv_noheader");
        csvFormat.innerText =
            '"' + prod_name + '"' + ',' + '"' + prod_sku + '"' + ',' + prod_stock + ',' + prod_price + ',' + '"' +
            prod_tax_status + '"' + ',' +
            '"' + prod_img_1 + '"' + ',' + '"' + prod_img_2 + '"' + ',' + '"' + prod_img_3 + '"' + ',' + '"' +
            prod_img_4 + '"' + ',' +
            prod_currency + ',' +
            '"' + prod_description + '"' + ',' + '"' + prod_unit + '"' + ',' + '"' + prod_department + '"' + ',' + '"' +
            prod_category + '"' + ',' + '"' + prod_brand + '"' + ',' + '"' + prod_segment + '"' + ',' +
            '"' + description.innerHTML + '"' + ',' + '"' + prod_guarantee + '"' + ',' + '"' + prod_contact + '"' +
            ',' + '"' + prod_contact_unit + '"' + ',' +
            dimension_length + ',' + dimension_width + ',' + dimension_height + ',' + dimension_weight;
        //csvFormat.innerText = csvFormat.innerHTML;

        var csvData = document.getElementById("prod_csv");
        csvData.innerText =
            "prod_name, prod_sku, prod_stock, prod_price, prod_tax_status, prod_img_1, prod_img_2, prod_img_3, prod_img_4," +
            " prod_currency, prod_description, prod_unit, prod_department, prod_category, prod_brand, prod_segment, prod_attributes, prod_guarantee," +
            " prod_contact, prod_contact_unit, dimension_length, dimension_width, dimension_height, dimension_weight" +
            '\r\n' + csvFormat.innerHTML;

        description.innerText = "";
        modal.style.display = "block";
    }

    function ModalDetail(prod_name, prod_sku, prod_stock, prod_price, prod_tax_status, prod_img_1, prod_img_2,
        prod_img_3, prod_img_4,
        prod_currency, prod_description, prod_unit,
        prod_department, prod_category, prod_brand, prod_segment, prod_attributes,
        prod_guarantee, prod_contact, prod_contact_unit,
        dimension_length, dimension_width, dimension_height, dimension_weight
    ) {
        // Replace `<br>` tags with actual newlines
        const processedAttributes = replaceModelString(decodeURIComponent(prod_attributes), "&lt;br&gt;", " | ");
        // console.log(prod_attributes);
        // console.log(processedAttributes);
        var modal = document.getElementById("window-detail");
        var name = document.getElementById("prod_name");
        name.innerText = prod_name;
        var sku = document.getElementById("prod_sku");
        sku.innerText = prod_sku;
        //var stock = document.getElementById("prod_stock");
        //stock.innerText = intlRound(prod_stock, 0);
        var stock = document.getElementById("prod_stock_1");
        stock.innerText = intlRound(prod_stock, 0);
        var price = document.getElementById("prod_price");
        price.innerText = intlRound(prod_price, 2);
        var price = document.getElementById("prod_tax_status");
        price.innerText = prod_tax_status;

        var prod_img = document.getElementById("prod_full_img");
        prod_img.src = prod_img_1;
        var prod_img = document.getElementById("prod_img_1");
        prod_img.src = prod_img_1;
        prod_img.alt = prod_name;
        var prod_img = document.getElementById("prod_img_2");
        prod_img.src = prod_img_2;
        prod_img.alt = replaceModelString(prod_img_2, "https://sedcolombia.com.co/Imagenes_Vtex/", " ");
        var prod_img = document.getElementById("prod_img_3");
        prod_img.src = prod_img_3;
        prod_img.alt = replaceModelString(prod_img_3, "https://sedcolombia.com.co/Imagenes_Vtex/", " ");
        var prod_img = document.getElementById("prod_img_4");
        prod_img.src = prod_img_4;
        prod_img.alt = replaceModelString(prod_img_4, "https://sedcolombia.com.co/Imagenes_Vtex/", " ");

        var currency = document.getElementById("prod_currency");
        currency.innerText = prod_currency;

        var description = document.getElementById("prod_description");
        description.innerText = prod_description;
        var description = document.getElementById("prod_guarantee");
        description.innerText = prod_guarantee;
        var description = document.getElementById("prod_attributes");
        description.innerHTML = processedAttributes;

        var unit = document.getElementById("prod_unit");
        unit.innerText = prod_unit;

        var group = document.getElementById("prod_department");
        group.innerText = prod_department;
        var group = document.getElementById("prod_category");
        group.innerText = prod_category;
        var group = document.getElementById("prod_brand");
        group.innerText = prod_brand;
        var group = document.getElementById("prod_segment");
        group.innerText = prod_segment;

        var contact = document.getElementById("prod_contact");
        contact.innerText = prod_contact;
        var contact = document.getElementById("prod_contact_unit");
        contact.innerText = prod_contact_unit;

        var dimension = document.getElementById("dimension_length");
        dimension.innerText = intlRound(dimension_length, 0);
        var dimension = document.getElementById("dimension_width");
        dimension.innerText = intlRound(dimension_width, 0);
        var dimension = document.getElementById("dimension_height");
        dimension.innerText = intlRound(dimension_height, 0);
        var dimension = document.getElementById("dimension_weight");
        dimension.innerText = intlRound(dimension_weight, 0);

        var csvFormat = document.getElementById("prod_csv");
        csvFormat.innerText =
            "prod_name, prod_sku, prod_stock, prod_price, prod_tax_status, prod_img_1, prod_img_2, prod_img_3, prod_img_4," +
            " prod_currency, prod_description, prod_unit, prod_department, prod_category, prod_brand, prod_segment, prod_attributes, prod_guarantee," +
            " prod_contact, prod_contact_unit, dimension_length, dimension_width, dimension_height, dimension_weight" +
            '\r\n' +
            '"' + prod_name + '"' + ',' + '"' + prod_sku + '"' + ',' + prod_stock + ',' + prod_price + ',' + '"' +
            prod_tax_status + '"' + ',' +
            '"' + prod_img_1 + '"' + ',' + '"' + prod_img_2 + '"' + ',' + '"' + prod_img_3 + '"' + ',' + '"' +
            prod_img_4 + '"' + ',' +
            prod_currency + ',' +
            '"' + prod_description + '"' + ',' + '"' + prod_unit + '"' + ',' + '"' + prod_department + '"' + ',' + '"' +
            prod_category + '"' + ',' + '"' + prod_brand + '"' + ',' + '"' + prod_segment + '"' + ',' +
            '"' + description.innerHTML + '"' + ',' + '"' + prod_guarantee + '"' + ',' + '"' + prod_contact + '"' +
            ',' + '"' + prod_contact_unit + '"' + ',' +
            dimension_length + ',' + dimension_width + ',' + dimension_height + ',' + dimension_weight;


        // var csvFormat = document.getElementById("product_csv");
        // csvFormat.style.display = "none";

        modal.style.display = "block";
    }

    function hiddenButtons(groupId, groupName) {
        const productName = document.getElementById('current-group-name');
        const productTitle = document.getElementById('product-title');
        const productId = document.getElementById('current-group');
        const brands = document.getElementById('selected-brands');
        const brandsName = document.getElementById('selected-brands-name');
        const categories = document.getElementById('selected-categories');
        const categoriesName = document.getElementById('selected-categories-name');
        // console.log("group " + groupName);
        // console.log("hidden input");
        // console.log(hiddenInput.value);
        productTitle.textContent = groupName;
        productName.value = groupName;
        brands.value = "";
        brandsName.value = "";
        categories.value = "";
        categoriesName.value = "";
        productId.value = groupId;
        // console.log("id " + groupId);
        // console.log("hidden id");
        // console.log(productId.value);
    }

    function addBrandToList(checkbox, brandName, groupName) {
        const brandListElement = document.getElementById('selected-brands');
        const brandNamesElement = document.getElementById('selected-brands-name');
        let selectedBrands = brandListElement.value ? brandListElement.value.split(',') :
    []; // Get existing or create empty array
        let selectedBrandsName = brandNamesElement.value ? brandNamesElement.value.split(',') : [];
        if (checkbox.checked) {
            selectedBrands.push(checkbox.value); // Add brand name if checkbox is checked
            selectedBrandsName.push(checkbox.value);
        } else {
            selectedBrands = selectedBrands.filter(brand => brand !== checkbox.value); // Remove brand name if unchecked
            selectedBrandsName = selectedBrands.filter(brand => brand !== brandName);
        }
        brandListElement.value = selectedBrands.join(','); // Update comma-separated list
        if (selectedBrands.length > 0)
            temporalIndicator();
        productBrandCards(groupName); // Call fetchProductsByBrands function and create cards
        brandNamesElement.value = selectedBrandsName.join('-');
        const productTitle = document.getElementById('product-title');
        productTitle.textContent = brandNamesElement.value;

    }

    function addCategoryToList(checkbox, brandName, groupName) {
        const catListElement = document.getElementById('selected-categories');
        const catNamesElement = document.getElementById('selected-categories-name');
        let selectedCategories = catListElement.value ? catListElement.value.split(',') :
    []; // Get existing or create empty array
        let selectedCatsName = catNamesElement.value ? catNamesElement.value.split(',') : [];
        if (checkbox.checked) {
            selectedCategories.push(checkbox.value); // Add brand name if checkbox is checked
            selectedCatsName.push(checkbox.value);
        } else {
            selectedCategories = selectedCategories.filter(brand => brand !== checkbox
                .value); // Remove brand name if unchecked
            selectedCatsName = selectedBrands.filter(brand => brand !== brandName);
        }
        catListElement.value = selectedCategories.join(','); // Update comma-separated list
        if (selectedCategories.length > 0)
            temporalIndicator();
        productCategoriesCards(groupName); // Call fetchProductsByBrands function and create cards
        catNamesElement.value = selectedCatsName.join('-');
        const productTitle = document.getElementById('product-title');
        productTitle.textContent = catNamesElement.value;

    }
    /*
        // read department name
        function DepartmentName() {
            const departmentName = document.getElementById('current-group-name');
            return departmentName.value;
        }
    */


    function productBrandCards(groupName) {
        fetchProductsByBrands(groupName)
            .then(products => generateCards(products))
            .catch(error => console.error(error));
    }

    function productCategoriesCards(groupName) {
        fetchProductsByCategories(groupName)
            .then(products => generateCards(products))
            .catch(error => console.error(error));
    }

    function temporalIndicator() {
        const cardsContainer = document.getElementById("products-container");
        cardsContainer.innerHTML = ""; // Clear existing content
        const temporal = document.createElement("div");
        temporal.id = "temporal";
        temporal.classList.add("temporal");
        const temporal_title = document.createElement("label");
        temporal_title.textContent = "En Proceso ...";
        const temporal_progress = document.createElement("progress");
        temporal_progress.max = 100;
        temporal_progress.value = 70;
        temporal.appendChild(temporal_title);
        temporal.appendChild(temporal_progress);
        cardsContainer.appendChild(temporal);
    }

    // create visual cards
    function generateCards(products) {
        const cardsContainer = document.getElementById("products-container");
        cardsContainer.innerHTML = ""; // Clear existing content

        if (products.length > 0) {
            products.forEach(product => {
                const card = document.createElement("div");
                card.classList.add("card");

                const cardBody = document.createElement("div");
                cardBody.classList.add("card-body");

                const quantity = document.createElement("div");
                quantity.classList.add("quantity");
                quantity.textContent = intlRound(product.stock_quantity, 0);

                const cardTitle = document.createElement("h6");
                cardTitle.classList.add("card-title");
                cardTitle.textContent = product.name;

                const cardImage = document.createElement("div");
                cardImage.classList.add("card-image");

                const image = document.createElement("img");
                image.src = product.image_1;
                image.alt = product.name;

                cardImage.appendChild(image);

                const cardBodyText = document.createElement("div");
                cardBodyText.classList.add("card-body-text");

                const cardText = document.createElement("div");
                cardText.classList.add("card-text");

                const button = document.createElement("button");
                // ModalDetail function call with product data
                button.onclick = function() {
                    ModalDetail(`${product.name}`, `${product.sku}`, product.stock_quantity, product
                        .regular_price, product.price_tax_status,
                        product.image_1, product.image_2, product.image_3, product.image_4,
                        product.currency, `${product.description}`, product.unit,
                        product.department, `${product.category}`, product.brand, product.segment,
                        `${product.attributes}`, product.guarantee, product.contact_agent, product
                        .contact_unit,
                        product.dimension_length, product.dimension_width, product.dimension_height,
                        product.dimension_weight
                    );
                };

                button.textContent = product.sku + ' / ' + product.brand;

                const button2 = document.createElement("button");
                // email product data
                button2.onclick = function() {
                    productMail(product.sku);
                };
                const icon = document.createElement("i");
                icon.classList.add("fas fa-envelope navitem-icon");
                button2.appendChild(icon);

                const priceText = document.createElement("div");
                priceText.classList.add("card-text");
                priceText.textContent = '$ ' + intlRound(product.regular_price, 2) + ' ' + product.currency +
                    ' / ' + product.unit + ' ' + product.price_tax_status;

                cardText.appendChild(button);
                cardText.appendChild(button2);
                cardText.appendChild(priceText);

                cardBodyText.appendChild(cardText);

                cardBody.appendChild(quantity);
                cardBody.appendChild(cardTitle);
                cardBody.appendChild(cardImage);
                cardBody.appendChild(cardBodyText);

                card.appendChild(cardBody);

                cardsContainer.appendChild(card);
            });
        } else {
            const card = document.createElement("h1");
            card.textContent = "No hay productos para la selección";
            cardsContainer.appendChild(card);
        }
    }



    // fetch products by brand
    function fetchProductsByBrands(groupName) {
        const selectedBrands = document.getElementById('selected-brands').value;
        return new Promise((resolve, reject) => {
            fetch(`/products/brands/${groupName}/${selectedBrands}`)
                .then(response => response.json())
                .then(products => {
                    resolve(products);
                })
                .catch(error => {
                    console.error('Error fetching products by brands:', error);
                    reject(error); // Pass error to the calling function
                });

        });
    }

    // fetch products by brand
    function fetchProductsByCategories(groupName) {
        const selectedCategories = document.getElementById('selected-categories').value;
        return new Promise((resolve, reject) => {
            fetch(`/products/categories/${groupName}/${selectedCategories}`)
                .then(response => response.json())
                .then(products => {
                    resolve(products);
                })
                .catch(error => {
                    console.error('Error fetching products by brands:', error);
                    reject(error); // Pass error to the calling function
                });

        });
    }

    function departmentRoute(groupName) {
        temporalIndicator();
        const productTitle = document.getElementById('product-title');
        productTitle.textContent = groupName;
        //window.location.href = '/products/' + groupName;
        window.location.href = "{{ route('product.show', ['group' => ':group']) }}".replace(':group', groupName);
    }

    //replace ' for avoid arguments warnings
    function cleanQuotation(text) {
        text.replace(/'/g, '"');
        //alert(`${text}`);
        //console.log(text);
        return text;
    }

    //convert Json to Csv format
    function jsonToCsv(data) {
        return (
            Object.keys(data[0]).join(",") +
            "\n" +
            data.map((d) => Object.values(d).join(",")).join("\n")
        );
    }

    //convert array to CSV format
    function arrayToCSV(data) {
        var csv = data.map(function(row) {
            return row.join(',');
        }).join('\n');
        return csv;
    }

    function fetchAbilities(username) {
        //window.location.href = "{{ route('profile.abilities', ['username' => ':username']) }}".replace(':username', username);
        urlpath = "{{ route('profile.abilities', ['username' => ':username']) }}".replace(':username', username);
        alert(urlpath);
        new Promise((resolve, reject) => {
            fetch(urlpath, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    //body: JSON.stringify(inputs), //body only with POST method and body format = Header Content-Type format
                })
                .then(response => response.json())
                .then(data => {
                    resolve(data);
                    document.getElementById("products-container").innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching sanctum abilities:', error);
                    reject(error); // Pass error to the calling function
                });
        });
    }



    // pvr willcards start example  for id property. coul by class, etc: const startsAbc = document.querySelectorAll("[id^='abc']");
    // const buttons = document.querySelectorAll('.department-button');
    // buttons.forEach(button => button
    //     .addEventListener('click', () => departmentActions(button)));


    function productMail(sku) {
        if (sku === '') {
            sku = document.getElementById("prod_sku").textContent;
        }

        let receiver = prompt("Correo del destinatario:");
        if (receiver.indexOf('@') !== -1) {
            const urlpath = "{{ route('product.email', ['sku' => ':sku']) }}".replace(':sku', sku);
            console.log(urlpath);

            fetch(urlpath, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'x-api-receiver': receiver,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    //resolve(data);
                    if (data['code'] === 200) alert("correo enviado");
                    if (data['code'] === 404) alert(data['result']);
                    console.log(data);
                })
                .catch(error => {
                    // Check for 404 response specifically
                    if (error.response && error.response.status === 404) {
                        alert("Product not found! Could not send email.");
                        console.error('Product not found:', error.response.data); // Log error details for debugging
                    } else {
                        console.error('Error fetching mail:', error); // Log other errors
                    }
                });
        }
    }


    function changeFullImage(image) {
        const mainImage = document.getElementById("prod_full_img");
        mainImage.src = image.src;
        mainImage.alt = image.alt;
    }

    function productCSV() {
        document.getElementById('window-csv').display = 'block';
    }
</script>

</html>
