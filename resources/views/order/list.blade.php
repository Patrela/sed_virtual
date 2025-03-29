@extends('layouts.page',  ['profile_list' =>  $profile_list, 'rolevalue' => $rolevalue])

@section('title', 'Orders List')
@section('content')
    <main>
        <aside>
            <div class="aside-container max-w-sm mx-auto sm:px-4 lg:px-6 space-y-4" name="main_group"> <!-- Fixed classes and reduced width -->
                {{-- <div class="py-12"> --}}
                    <!-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> 4 por 12-->
                    <!-- <div class="max-w-xs mx-auto sm:px-4 lg:px-6 space-y-4"> 8 por 2 -->
                        <div class="p-4 sm:p-4 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section class="space-y-6">
                                    <header>
                                        <header>
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Orders') }}
                                            </h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ __("Orders by date.") }}
                                            </p>
                                        </header>

                                        <form id="orderForm" class="mt-6 space-y-6">
                                            @csrf
                                            @method('GET')

                                            <div>
                                                <x-input-label for="order" :value="__('Order')" />
                                                <x-text-input id="order" name="order" type="text" class="mt-1 block w-full"
                                                    autofocus autocomplete="order number"
                                                />
                                                <x-input-error class="mt-2" :messages="$errors->get('order')" />
                                            </div>

                                            <div>
                                                <x-input-label for="trade_nit" :value="__('Trade Nit')" />
                                                <x-text-input id="trade_nit" name="trade_nit" type="text"
                                                    class="mt-1 block w-full" autofocus autocomplete="trade_nit"
                                                />
                                                <x-input-error class="mt-2" :messages="$errors->get('trade_nit')" />
                                            </div>

                                            <div>
                                                <x-input-label for="start" :value="__('Start Date')" />
                                                <x-date-input id="start" name="start" type="date" class="mt-1 block w-full"
                                                    autocomplete="start date" />
                                            </div>

                                            <div>
                                                <x-input-label for="end" :value="__('End Date')" />
                                                <x-date-input id="end" name="end" type="date" class="mt-1 block w-full"
                                                    autocomplete="end date" />
                                            </div>


                                            <div class="flex items-center gap-4">
                                                <x-primary-button>{{ __('Search Orders') }}</x-primary-button>
                                            </div>
                                            <div id="save_message"
                                                class="flex items-center gap-4 mt-1 text-lg font-medium text-gray-900">
                                            </div>
                                        </form>
                                </section>

                            </div>
                        </div>
                    <!-- </div> -->

                </div>

        </aside>
        <section class="main-section" name="main_group" id="main_group">
            <div class="table-row" id="developer-container">

                <div class="modal-card-item-division">
                    <!-- <div class="list-text-large"><strong>Order</strong>
                                            </div> -->
                    <div class="list-text-title">Date
                    </div>
                    <div class="list-text-title">Order
                    </div>
                </div>
                <div class="list-text-medium-title">Trade
                </div>
                <div class="list-text-regular-title">Sku
                </div>
                <div class="list-text-little-title">Quantity
                </div>
                <div class="list-text-regular-title">Value
                </div>               
            </div> 
             
            @foreach ($orders as $key => $order)
                <div class="table-row" id="developer-container">
                    <div class="list-text">{{ $order['transaction_date_time'] }}
                    </div>
                    <div class="list-text">{{ $order['n_order'] }}| {{ $order['trade_request'] }}
                    </div>
                    <div class="list-text-medium">{{ $order['trade'] }}
                    </div>
                    <div class="list-text-regular">{{ $order['sku'] }}
                    </div>
                    <div class="list-text-little">{{ $order['quantity'] }}
                    </div>
                    <div class="list-text-regular">{{ number_format($order['unit_price'], 2, ',', '.') }}
                    </div>                     
                    {{-- <div class="list-text"
                        onclick="openSkuDetailModal('{{ $order['name'] }}', '{{ $order['sku'] }}', '{{ $order['stock_quantity'] }}', '{{ $order['regular_price'] }}', '{{ $order['image_1'] }}')">
                        <p class="card-text">{{ $order['sku'] }}</p>
                        <p class="card-text">{{ '$ ' . number_format($order['regular_price'], 2, ",", ".") }}</p>
                    </div> --}}
                </div>
            @endforeach
            

        </section>


    </main>
    <script type="text/javascript">
        document.getElementById('orderForm').onsubmit = function (event) {
            event.preventDefault(); // Prevent the default form submission

            fetchOrders();
        };


        function clearOrderCard() {
            let item = document.getElementById('start');
            item.value = "";
            item = document.getElementById('end');
            item.value = "";
            item = document.getElementById('order');
            item.value = "";
            item = document.getElementById('trade_nit');
            item.value = "";
            item = document.getElementById('save_message');
            item.innerText = "";

        }

        function fetchOrders() {
            sender = "{{ Auth::user()->email}}";
            const ordernumber = document.getElementById('order').value.trim();
            const startdate = document.getElementById('start').value.trim();
            const enddate = document.getElementById('end').value.trim();
            const trade = document.getElementById('trade_nit').value.trim();

            if (ordernumber.length > 0) {
                fetchOrderNumber(ordernumber)

                // } else if (trade.length > 0 ) {
                //     fetchTradeOrder(trade, startdate, enddate);
                // }
                // else  if (startdate.length === 0 && enddate.length === 0) {
                //     let item = document.getElementById('save_message');
                //     item.innerText = "Please enter a valid order!";
                //     return;
                // }
                // else {
                //     fetchOrderDate(startdate, enddate);
            }
        }

/*
        // fetch products by brand
        function fetchOrderNumber(ordernumber) {
            //alert('order ' + ordernumber);
            const newpath = "{{ route('order.show', ['order' => ':order']) }}".replace(':order', encodeURIComponent(ordernumber));
            alert(newpath);
            console.log("Fetching order ", ordernumber);
            return new Promise((resolve, reject) => {
                //fetch(`/isolatedprofile.mail/${email}`)
                fetch(`${newpath}`)
                    .then(response => response.json())
                    .then(order => {
                        //console.log("1 ",user);
                        resolve(order);
                    })
                    .catch(error => {
                        console.error('Error fetching order by NUMBER:', error);
                        reject(error); // Pass error to the calling function
                    });

            });
        }
*/
        // fetch oders by number
        function fetchOrderNumber(ordernumber) {
            const newpath = "{{ route('order.show', ['order' => ':order']) }}".replace(':order', encodeURIComponent(ordernumber));
            //console.log("Fetching order ", ordernumber);
            fetch(newpath, {
                method: 'GET', // Specify the GET method explicitly
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(order => {
                    writeOrder(order);
                })
                .catch(error => {
                    console.error('Error fetching order by NUMBER:', error);
                });
        }

        function writeOrder(order) {
            const mainGroup = document.getElementById('main_group');
            mainGroup.innerHTML = ''; // Clear existing content

            // Start with order-level details
            let orderContent = `
                <div class="table-row">
                    <div class="list-text-title">transaction_date</div>
                    <div class="list-text-title">order & trade_code</div>
                    <div class="list-text-little-title">nit</div>
                    <div class="list-text-little-title">status</div>
                    <div class="list-text-title">buyer</div>
                    <div class="list-text-regular-title">CUS</div>
                </div>            
                <div class="table-row">
                    <div class="list-text">${order.transaction_date_time}</div>
                    <div class="list-text">${order.order_number} | ${order.trade_request_code}</div>
                    <div class="list-text-little">${order.trade_nit}</div>
                    <div class="list-text-little">${order.request_status}</div>
                    <div class="list-text">${order.buyer_name}</div>
                    <div class="list-text-regular">${order.transaction_cus}</div>
                </div>
                <br/>
                <div class="table-row">
                    <div class="list-text-little-title">#</div>
                    <div class="list-text-medium-title">sku & trade sku</div>
                    <div class="list-text-medium-title">product</div>
                    <div class="list-text-little-title">quantity</div>
                    <div class="list-text-regular-title">sed_unit_price</div>
                    <div class="list-text-little-title">currency</div>
                    <div class="list-text-little-title">has_tax</div>
                </div>
            `;

            // Append each item's details
            order.items.forEach(item => {
                orderContent += `
                    <div class="table-row">
                        <div class="list-text-little">${item.item}</div>
                        <div class="list-text-medium">${item.part_num} | ${item.trade_part_num}</div>
                        <div class="list-text-medium">${item.product_name}</div>
                        <div class="list-text-little">${item.quantity}</div>
                        <div class="list-text-regular">${number_format(item.sed_unit_price, 2, ',', '.')}</div>
                        <div class="list-text-little">${item.currency}</div>
                        <div class="list-text-little">${item.is_tax_applied ? 'Yes' : 'No'}</div>
                    </div>
                `;
            });
            // Write the final content to the main group
            mainGroup.innerHTML = orderContent;
        }
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = parseFloat(number).toFixed(decimals);
            const parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
            return parts.join(dec_point);
        }   
    </script>
@endsection