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
</head>

<body>
    <x-mainmenu />
    <main> <!--class="mt-6" -->
        <aside>
            <div class="aside-container" name="main_group" id="main_group">
                <input type="hidden" name="current-group" id="current-group" value="{{ $maingroup }}" />
                <input type="hidden" name="current-name-group" id="current-name-group" value="{{ $maingroup }}" />
                <h3 class="title">GRUPOS</h3>
                <div class="button-container">
                    @foreach ($departments as $key => $department)
                        <button
                            class="department-button {{ $maingroup == $department['id'] ? 'aside-button-active' : '' }}"
                            value="{{ $department['id'] }}" id="dep-{{ $department['id'] }}"
                            name="dep-{{ $department['id'] }}">{{ $department['name'] }}</button>
                        @if ($department['id'] == $maingroup)
                            <script type="text/javascript">
                                const hiddenInput = document.getElementById('current-name-group');
                                hiddenInput.value = "<?php echo $department['name']; ?>";
                                //alert(hiddenInput.value);
                            </script>
                        @endif
                    @endforeach
                </div>
                {{-- <button>Más &nbsp;&nbsp;<i class="mx-3 fas fa-caret-down"></i></a></button> --}}
            </div>
            <p></p>
            <div class="aside-container">
                <h3 class="title">FILTROS</h3>
                <button>Categoría</button>
                <div id="category_detail" name="category_detail" class="filter-container-two-column">
                    @foreach ($categories as $key => $category)
                        <div class="filter-checkbox">
                            <div class="filter-checkbox-label">
                                {{ $category['name'] }}
                            </div>
                            <div class="filter-checkbox-input">
                                <input id="cat-{{ $category['id'] }}" type="checkbox"
                                    name="cat-{{ $category['id'] }}" value="{{ $category['id'] }}" />
                            </div>
                        </div>
                    @endforeach

                </div>
                <button>Marca</button>
                <div id="brand_detail" name="brand_detail" class="filter-container">
                    @foreach ($brands as $key => $brand)
                        <div class="filter-checkbox">
                            <div class="filter-checkbox-label">
                                {{ $brand['name'] }}
                            </div>
                            <div class="filter-checkbox-input">
                                <input id="brand-{{ $brand['id'] }}" type="checkbox"
                                    name="brand-{{ $brand['id'] }}" value="{{ $brand['id'] }}" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <button>Segmento</button>
                <div id="brand_detail" name="brand_detail" class="filter-container-two-column">
                    @foreach ($segments as $key => $segment)
                        <div class="filter-checkbox">
                            <div class="filter-checkbox-label">
                                {{ $segment['name'] }}
                            </div>
                            <div class="filter-checkbox-input">
                                <input id="segment-{{ $segment['id'] }}" type="checkbox"
                                    name="segment-{{ $segment['id'] }}" value="{{ $segment['id'] }}" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <h3 class="title">LIMPIAR</h3>
                <div class="filter-container-one-column">
                    <div class="centered">Última actualización</div>
                    <div class="centered">
                        <strong>{{ session()->has('lastUpdated') ? session('lastUpdated') : date('d/m/Y H:i:s') }}</strong>
                    </div>
                    <div class="centered">
                        <form action="{{ route('refresh') }}" method="POST">
                            @csrf
                            <button type="submit">Cargar Ahora </button>
                        </form>
                    </div>
                </div>
            </div>

        </aside>
        <section class="main-section" id="main-section">
            <h2 class="product-title" id="product-title" name="product-title">Computadores
            </h2>
            <div class="table-row" id="productContainer">
                @php
                    $counter = 0;
                    $perPage = request()->has('perPage') ? request()->perPage : 30; //18;
                    //$page = session()->has('numberPage') ? session('numberPage') : 1;
                    $page = request()->has('page') ? request()->page : 1;
                    $start = ($page - 1) * $perPage;
                    $end = $start + $perPage > $total ? $total : $start + $perPage;
                    //dd($products);
                    //dd("pag. " .$page . "= " .$perPage );
                @endphp
                @foreach ($products as $key => $product)
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
                                    <button
                                        onclick="modalDetail('{{ $product['name'] }}', '{{ $product['sku'] }}',
                                    {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }}, '{{ $product['image_1'] }}',
                                    '{{ $product['currency'] }}', '{{ $product['description']}}', '{{ $product['unit'] }}',
                                    '{{ $product['department']}}', '{{ $product['category']}}', '{{ $product['brand']}}', '{{ $product['segment']}}',
                                     '{{ $product['attributes'] }}', '{{ $product['guarantee']}}', '{{ $product['contact_agent']}}', '{{ $product['contact_unit']}}',
                                     '{{ $product['image_2'] }}', '{{ $product['image_3'] }}', '{{ $product['image_4'] }}'
                                    )">
                                        {{ $product['sku'] }} </button>
                                        <button
                                        onclick="modal1('{{ $product['name'] }}', '{{ $product['sku'] }}',
                                    {{ $product['stock_quantity'] }}, {{ $product['regular_price'] }}, '{{ $product['image_1'] }}',
                                     '{{ $product['currency'] }}', '{{ $product['description']}}'
                                      )">
                                        {{ $product['currency'] }} </button>
                                </div>
                                <div class="card-text">
                                    {{ '$ ' . number_format($product['regular_price'], 2, ',', '.') }}
                                    {{ $product['currency'] }}  / {{ $product['unit'] }}
                                </div>
                            </div>
                        </div>
                        @php
                            $counter++;
                        @endphp
                    @endif
                @endforeach
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

    <div id="productWindow" class="modal" onclick="closeModal()">
        <div class="modal-container">
            <form id="skuDetailForm" class="modal-container" method="post" action="#" class="p-6">
                <div class="card">
                    <div class="card-body">
                        <div class="quantity" id="prod_stock"></div>
                        <h6 class="card-title" id="prod_name"></h6>
                        <div class="card-image">
                            <img id="prod_img_1" src="" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <p class="card-text" id="prod_sku"></p>
                        <p class="card-text" id="prod_price"></p>
                    </div>
                </div>
                <div>
                    <button id="cerrar" onclick="closeModal()">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="productWindow1" class="modal" onclick="closeModal()">
        {{-- <div class="modal-container"> --}}
            <form id="skuDetailForm1" class="modal-container" method="get" action="#" class="p-6">
                <div class="modal-card">
                    <div class="modal-card-title" id="prod_name1"></div>
                    <div class="card-body">
                        <div class="quantity" id="prod_stock1"></div>
                        <div class="modal-card-image">
                            <img id="prod_img_11" src="" alt="">
                        </div>
                    </div>
                </div>
                <div class="filter-container-two-column">
                    <p class="card-text" id="prod_sku1"></p>
                    <p class="card-text" id="prod_price1"></p>
                    <p class="card-text" id="prod_currency1"></p>
                    <p class="card-text" id="prod_description1"></p>
                </div>
                <div class="filter-container-one-column">
                    <button id="cerrar" onclick="closeModal()">Cerrar</button>
                </div>
            </form>
    </div>
    {{-- <div id="productWindow1" class="modal" onclick="closeModal()">
        <div class="modal-container">
            <form id="skuDetailForm1" class="modal-container" method="get" action="#" class="p-6">
                <div class="card">
                    <div class="card-body">
                        <div class="quantity" id="prod_stock1"></div>
                        <h6 class="card-title" id="prod_name1"></h6>
                        <div class="card-image">
                            <img id="prod_img_11" src="" alt="">
                        </div>
                    </div>
                    <div class="card-body-text">
                        <p class="card-text" id="prod_sku1"></p>
                        <p class="card-text" id="prod_price1"></p>
                        <p class="card-text" id="prod_currency1"></p>
                        <p class="card-text" id="prod_description1"></p>

                    </div>
                </div>
                <div>
                    <button id="cerrar" onclick="closeModal()">Cerrar</button>
                </div>
            </form>
        </div>
    </div>     --}}
</body>


<script type="text/javascript">
    // Si el usuario hace click en la x, la ventana se cierra
    function closeModal() {
        var modal = document.getElementById("productWindow");
        modal.style.display = "none";
        /* PVR temporal */
        var modal = document.getElementById("productWindow1");
        modal.style.display = "none";
    };

    //  modalDetail(prod_name, prod_sku, prod_stock, prod_price, prod_img_1,
    //                                     prod_currency, prod_description, prod_unit,
    //                                     prod_department, prod_category, prod_brand, prod_segment,
    //                                     prod_attributes, prod_guarantee, prod_contact, prod_contact_unit,
    //                                     prod_img_2, prod_img_3, prod_img_4)
    function modalDetail(prod_name, prod_sku, prod_stock, prod_price, prod_img_1,
    prod_currency, prod_description, prod_unit,
    prod_department, prod_category, prod_brand, prod_segment,
    prod_attributes, prod_guarantee, prod_contact, prod_contact_unit,
    prod_img_2, prod_img_3, prod_img_4) {
        // Ventana modal
        var modal = document.getElementById("productWindow");
        var name = document.getElementById("prod_name");
        name.innerText = prod_name;
        var sku = document.getElementById("prod_sku");
        sku.innerText = prod_sku;
        var stock = document.getElementById("prod_stock");
        stock.innerText = prod_stock;
        // var stock = document.getElementById("prod_stock_2");
        // stock.innerText = prod_stock;
        var price = document.getElementById("prod_price");
        price.innerText = prod_price;
        var prod_img = document.getElementById("prod_img_1");
        prod_img.src = prod_img_1;
        modal.style.display = "block";
    }
    function modal1(prod_name, prod_sku, prod_stock, prod_price, prod_img_1,
    prod_currency, prod_description
    ) {
        var modal = document.getElementById("productWindow1");
        var name = document.getElementById("prod_name1");
        name.innerText = prod_name;
        var sku = document.getElementById("prod_sku1");
        sku.innerText = prod_sku;
        var stock = document.getElementById("prod_stock1");
        stock.innerText = prod_stock;
        // var stock = document.getElementById("prod_stock_2");
        // stock.innerText = prod_stock;
        var price = document.getElementById("prod_price1");
        price.innerText = prod_price;
        var prod_img = document.getElementById("prod_img_11");
        prod_img.src = prod_img_1;
        var currency = document.getElementById("prod_currency1");
        currency.innerText = prod_currency;
        var description = document.getElementById("prod_description1");
        description.innerText = prod_description;

        modal.style.display = "block";
    }

    function hiddenButtons(groupId, groupName) {
        const hiddenInput = document.getElementById('current-name-group');
        const productTitle = document.getElementById('product-title');
        const productId = document.getElementById('current-group');
        console.log("group " + groupName);
        console.log("hidden input");
        console.log(hiddenInput.value);
        productTitle.innerHTML = groupName;
        hiddenInput.value = groupName;
        productId.value = groupId;
        console.log("id " + groupId);
        console.log("hidden id");
        console.log(productId.value);
    }

    function departmentActions(currentButton) {
        const buttons = document.querySelectorAll('.department-button');
        buttons.forEach(otherButton => {
            //toggle_menu
            if (otherButton !== currentButton) {
                if (otherButton.classList.contains('aside-button-active')) {
                    otherButton.classList.remove('aside-button-active'); // Reset hover class
                }
            }
            //load categories
        });
        //update hidden buttons
        hiddenButtons(currentButton.value, currentButton.innerHTML);
        categoriesTags(currentButton.value);
        producstCards(currentButton.value, currentButton.innerHTML);
    }
    //load categories by department ID
    function categoriesTags(groupId) {
        const categoryDetail = document.getElementById('category_detail');
        categoryDetail.innerHTML = '';
        fetchCategoriesByDepartment(groupId)
            .then(categories => {
                categories.forEach(category => {
                    // Build category detail HTML based on category data
                    const categoryHtml = `
                    <div class="filter-checkbox">
                        <div class="filter-checkbox-label">
                            ${category['name']}
                        </div>
                        <div class="filter-checkbox-input">
                            <input id="cat-${category['id']}" type="checkbox" name="cat-${category['id']}" value="${category['id']}" />
                        </div>
                    </div>
                    `;
                    categoryDetail.insertAdjacentHTML('beforeend', categoryHtml);
                });
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
            });

    }
    // read Products by Department and show in cards
    function producstCards(groupId, groupName) {
        const productDetail = document.getElementById('productContainer');
        productDetail.innerHTML = "";
        fetchDepartmentProducts(groupName)
            .then(products => {
                products.forEach(product => {
                    // Build product detail HTML based on product data
                    const productHTML = `
                    <div class="card">
                        <div class="card-body">
                            <div class="quantity">${product['stock_quantity']}</div>
                            <h6 class="card-title">${product['name']} - ${product['stock_quantity']}</h6>
                            <div class="card-image">
                                <img src="${product['image_1']}" alt="${product['name']}">
                            </div>
                        </div>
                        <div class="card-body-text">
                            <div class="card-text">
                                <button class="modal-button card-text"
                                    onclick="modal1('${product['name']}',' ${product['sku']}', '${product['stock_quantity']}',
                                    '${product['regular_price']}', '${product['image_1']}')"
                                >{{ $product['sku'] }} </button>
                            </div>
                            <div class="card-text">$ ${product['regular_price']}
                            </div>
                        </div>
                    </div>`;
                    productDetail.insertAdjacentHTML('beforeend', productHTML);
                });
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
            });

    }
    //Function to fetch categories by department ID
    function fetchCategoriesByDepartment(departmentId) {
        return new Promise((resolve, reject) => {
            fetch(`/categories/parent/${departmentId}`) // Replace with your actual route
                .then(response => response.json())
                .then(categories => {
                    resolve(categories);
                })
                .catch(error => {
                    console.error('Error fetching categories:', error);
                    reject(error); // Pass error to the calling function
                });
        });
    }
    //Function to fetch prodicts by department ID
    function fetchDepartmentProducts(group) {
        return new Promise((resolve, reject) => {
            fetch(`/products/${group}`) // Replace with your actual route
                .then(response => response.json())
                .then(products => {
                    resolve(products);
                })
                .catch(error => {
                    console.error('Error fetching department products:', error);
                    reject(error); // Pass error to the calling function
                });
        });
    }

    // pvr willcards start example  for id property. coul by class, etc: const startsAbc = document.querySelectorAll("[id^='abc']");
    const buttons = document.querySelectorAll('.department-button');
    buttons.forEach(button => button.addEventListener('click', () => departmentActions(button)));


    // const menuToggle = document.querySelector('.menu-toggle');
    // const mobileMenu = document.querySelector('.mobile-menu');
    // const menuAside = document.querySelector('.aside-menu-toggle');


    // menuToggle.addEventListener('click', () => {
    //     mobileMenu.classList.toggle('active');
    //     menuToggle.classList.toggle('active');
    //     menuAside.classList.toggle('active');
    // });
</script>

</html>
