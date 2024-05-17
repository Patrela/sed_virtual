<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/ppal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
</head>

<body>
    <x-mainmenu />
    <main > <!--class="mt-6" -->
        <aside>
            <div class="aside-container" name="main_group"  id="main_group">
                <input type="hidden" name="current-group"  id="current-group" value="{{$maingroup}}" />
                <input type="hidden" name="current-group"  id="current-group" value="{{$maingroup}}" />
                <h3 class="title">GRUPOS</h3>
                @foreach ($departments as $key => $department)
                    <button class="department-button {{($maingroup==$department['id'])? 'aside-button-active': ''}}" value="{{$department['id']}}" id="dep-{{$department['id']}}" name="dep-{{$department['id']}}">{{$department['name']}}</button>
                    @if ($department['id']==$maingroup)
                        <input type="hidden" name="current-name-group"  id="current-name-group" value="{{$department['name']}}" />
                    @endif
                @endforeach
                <button>Más &nbsp;&nbsp;<i class="mx-3 fas fa-caret-down"></i></a></button>
            </div>
            <p></p>
            <div class="aside-container" name="main_filter"  id="main_filter">
                <h3 class="title">FILTROS</h3>
                <button>Categoría</button>
                    <div id="category_detail" name="category_detail">
                    </div>
                <button>Marca</button>
                <div id="brand_detail" name="brand_detail" class="filter-container">
                    @foreach ($brands as $key => $brand)
                    <div class="filter-checkbox">
                        <div class="filter-checkbox-label">
                           {{$brand['name']}}
                        </div>
                        <div class="filter-checkbox-input">
                            <input id="brand-{{$brand['id']}}" type="checkbox" name="brand-{{$brand['id']}}" value="{{$brand['id']}}"/>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button>Segmento</button>
                    <div id="brand_detail" name="brand_detail" class="filter-container">
                        @foreach ($segments as $key => $segment)
                        <div class="filter-checkbox">
                            <div class="filter-checkbox-label">
                                {{$segment['name']}}
                            </div>
                            <div class="filter-checkbox-input">
                                <input id="segment-{{$segment['id']}}" type="checkbox" name="segment-{{$segment['id']}}" value="{{$segment['id']}}"/>
                            </div>
                        </div>
                        @endforeach
                    </div>
            </div>
        </aside>
        <section class="main-section">
                <h2  class="product-title" id="product-title" name="product-title">
                </h2>
                <script type="text/javascript">
                    // Assuming the hidden input is already present in the HTML
                    const hiddenInput = document.getElementById('current-name-group');
                    const productTitle = document.getElementById('product-title');

                    // Set the inner HTML of product-title to the value of the hidden input
                    productTitle.innerHTML = hiddenInput.value;
                </script>
            <div class="table_row" id="productContainer">
                @php
                    $counter = 0;
                    $perPage =  request()->has('perPage') ? request()->perPage : 30; //18;
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
                            <div class="body_card">
                                <div class="quantity">
                                    {{ number_format($product['stock_quantity'], 0, ",", ".") }}
                                </div>
                                <h6 class="card_title">{{ $product['name'] }} - {{ $product['stock_quantity'] }}</h6>
                                <div class="card_image" >
                                    <img src="{{ $product['image_1'] }}" alt="{{ $product['name'] }}">
                                </div>
                            </div>
                            <div class="body_text"
                            onclick="openSkuDetailModal('{{ $product['name'] }}', '{{ $product['sku'] }}', '{{ $product['stock_quantity'] }}', '{{ $product['regular_price'] }}', '{{ $product['image_1'] }}')"
                            >
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
        </section>
    </main>
    <script type="text/javascript">
        // Assuming the hidden input is already present in the HTML
        const hiddenInput = document.getElementById('current-name-group');
        const productTitle = document.getElementById('product-title');

        // Set the inner HTML of product-title to the value of the hidden input
        productTitle.innerHTML = hiddenInput.value;
    </script>
    <script>
        const departmentButtons = document.querySelectorAll('.department-button');
        const categoryDetail = document.getElementById('category_detail');

        departmentButtons.forEach(button => {
        button.addEventListener('focusin', function() {
        // button.addEventListener('click', function() {
            const departmentId = this.value;
            if(!categoryDetail.classList.contains('filter-container')) categoryDetail.classList.add('filter-container');
            categoryDetail.innerHTML = ''; // Clear previous content
            // var categories=fetchCategoriesByDepartment(departmentId);
            // Assuming you have a function to fetch categories by department ID
            fetchCategoriesByDepartment(departmentId)
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
        });
        });


        // Function to fetch categories by department ID (replace with your implementation)
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

      </script>
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const menuAside = document.querySelector('.aside-menu-toggle');


        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
            menuAside.classList.toggle('active');
        });
    </script>
</body>

</html>
