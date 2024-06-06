function filterProducts(products, selectedCategories, selectedBrands) {
    return products.filter(product => {
        const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(product.category);
        const brandMatch = selectedBrands.length === 0 || selectedBrands.includes(product.brand);
        return categoryMatch && brandMatch;
    });
}
function sortAndFilterProducts(products, criteria, selectedCategories, selectedBrands) {
    // First filter the products
    let filteredProducts = filterProducts(products, selectedCategories, selectedBrands);

    // Then sort the filtered products
    switch(criteria) {
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
    const selectedCategories = Array.from(document.querySelectorAll('input[name="cat-array"]:checked')).map(cb => cb.value);
    const selectedBrands = Array.from(document.querySelectorAll('input[name="brand-array"]:checked')).map(cb => cb.value);
    const orderCriteria = document.getElementById('order-options').value;

    return { selectedCategories, selectedBrands, orderCriteria };
}

function updateProductsDisplay() {

    const { selectedCategories, selectedBrands, orderCriteria } = getSelectedFilters();
    // update the title of filters
    const groupElement = document.getElementById('current-group-name');
    const title = groupElement.value;
    let categories = "";
    let brands = "";
    if (selectedCategories.length > 0) { categories = ` ${selectedCategories.join('-')}`; }
    if (selectedBrands.length > 0) { brands = ` ${selectedBrands.join('-')}`; }
    //alert('Update Products ' + title + ' '+ categories + ' ' + brands );
    const titleElement = document.getElementById('product-title');
    titleElement.textContent = `${title}${categories}${brands}`;

    const filteredAndSortedProducts = sortAndFilterProducts(allProducts, orderCriteria, selectedCategories, selectedBrands);
    generateCards(filteredAndSortedProducts);
}

    // Si el usuario hace click en la x, la ventana se cierra
    function closeModal(windowForm) {
        var modal = document.getElementById(`${windowForm}`);
        modal.style.display = "none";
    }
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

    function getString(text) { return '"' + text + '",' ;}

    function getNumber(numeric) { return '' + numeric + ',' ; }

    function productFormCSV(
        prod_name,
        prod_sku,
        prod_stock,
        prod_price,
        prod_tax_status,
        prod_img_1,
        prod_img_2,
        prod_img_3,
        prod_img_4,
        prod_currency,
        prod_description,
        prod_unit,
        prod_department,
        prod_category,
        prod_brand,
        prod_segment,
        prod_attributes,
        prod_guarantee,
        prod_contact,
        prod_contact_unit,
        dimension_length,
        dimension_width,
        dimension_height,
        dimension_weight
    ) {
        var modal = document.getElementById("window-csv");
        const description = document.getElementById("prod_csv_desc");
        // Replace `<br>` tags
        description.innerHTML = replaceModelString( decodeURIComponent(prod_attributes),"&lt;br&gt;", " | " );

        var csvFormat = document.getElementById("prod_csv_noheader");
        csvFormat.innerText =
        getString(prod_name) + getString(prod_sku) + getNumber(prod_stock) + getNumber(prod_price) + getString(prod_tax_status) +
        getString(prod_img_1) + getString(prod_img_2) + getString(prod_img_3) + getString(prod_img_4) +
        getNumber(prod_currency)  + getString(prod_description) + getString(prod_unit) +
        getString(prod_department) + getString(prod_category) + getString(prod_brand) + getString(prod_segment) +
        getString(description.innerHTML)  + getString(prod_guarantee) + getString(prod_contact) + getString(prod_contact_unit) +
        getNumber(dimension_length) + getNumber(dimension_width) + getNumber(dimension_height) + dimension_weight;
        //csvFormat.innerText = csvFormat.innerHTML;

        var csvData = document.getElementById("prod_csv");
        csvData.innerText =
            "prod_name,prod_sku,prod_stock,prod_price,prod_tax_status,prod_img_1,prod_img_2,prod_img_3,prod_img_4, " +
            "prod_currency,prod_description,prod_unit,prod_department,prod_category,prod_brand,prod_segment,prod_attributes,prod_guarantee, " +
            "prod_contact,prod_contact_unit,dimension_length,dimension_width,dimension_height,dimension_weight" +
            "\r\n" + csvFormat.innerHTML;

        description.innerText = "";
        modal.style.display = "block";
    }

    function ModalDetail(
        prod_name,
        prod_sku,
        prod_stock,
        prod_price,
        prod_tax_status,
        prod_img_1,
        prod_img_2,
        prod_img_3,
        prod_img_4,
        prod_currency,
        prod_description,
        prod_unit,
        prod_department,
        prod_category,
        prod_brand,
        prod_segment,
        prod_attributes,
        prod_guarantee,
        prod_contact,
        prod_contact_unit,
        dimension_length,
        dimension_width,
        dimension_height,
        dimension_weight
    ) {
        // Replace `<br>` tags with actual newlines
        const processedAttributes = replaceModelString( decodeURIComponent(prod_attributes),"&lt;br&gt;"," | ");
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
        prod_img.alt =  prod_img_2.substring(prod_img_2.lastIndexOf("/") + 1);
        var prod_img = document.getElementById("prod_img_3");
        prod_img.src = prod_img_3;
        prod_img.alt = prod_img_3.substring(prod_img_3.lastIndexOf("/") + 1);
        var prod_img = document.getElementById("prod_img_4");
        prod_img.src = prod_img_4;
        prod_img.alt = prod_img_4.substring(prod_img_4.lastIndexOf("/") + 1);

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
            " prod_contact, prod_contact_unit, dimension_length, dimension_width, dimension_height, dimension_weight"
            + "\r\n" +
            getString(prod_name) + getString(prod_sku) + getNumber(prod_stock) + getNumber(prod_price) + getString(prod_tax_status) +
            getString(prod_img_1) + getString(prod_img_2) + getString(prod_img_3) + getString(prod_img_4) +
            getNumber(prod_currency)  + getString(prod_description) + getString(prod_unit) +
            getString(prod_department) + getString(prod_category) + getString(prod_brand) + getString(prod_segment) +
            getString(description.innerHTML)  + getString(prod_guarantee) + getString(prod_contact) + getString(prod_contact_unit) +
            getNumber(dimension_length) + getNumber(dimension_width) + getNumber(dimension_height) + dimension_weight;

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
            products.forEach((product) => {
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

                const btnModal = document.createElement("button");
                // ModalDetail function call with product data
                btnModal.onclick = function () {
                    ModalDetail(
                        `${product.name}`,
                        `${product.sku}`,
                        product.stock_quantity,
                        product.regular_price,
                        product.price_tax_status,
                        product.image_1,
                        product.image_2,
                        product.image_3,
                        product.image_4,
                        product.currency,
                        `${product.description}`,
                        product.unit,
                        product.department,
                        `${product.category}`,
                        product.brand,
                        product.segment,
                        `${product.attributes}`,
                        product.guarantee,
                        product.contact_agent,
                        product.contact_unit,
                        product.dimension_length,
                        product.dimension_width,
                        product.dimension_height,
                        product.dimension_weight
                    );
                };

                btnModal.textContent = product.sku + " / " + product.brand;

                const btnMail = document.createElement("button");
                // email product data
                btnMail.onclick = function () {
                    productMail(product.sku);
                };
                const icon = document.createElement("i");
                icon.classList.add("fas");
                icon.classList.add("fa-envelope");
                icon.classList.add("navitem-icon");

                btnMail.appendChild(icon);

                const btnCSV = document.createElement("button");
                // email product data
                btnCSV.onclick = function () {
                    productFormCSV(
                        `${product.name}`,
                        `${product.sku}`,
                        product.stock_quantity,
                        product.regular_price,
                        product.price_tax_status,
                        product.image_1,
                        product.image_2,
                        product.image_3,
                        product.image_4,
                        product.currency,
                        `${product.description}`,
                        product.unit,
                        product.department,
                        `${product.category}`,
                        product.brand,
                        product.segment,
                        `${product.attributes}`,
                        product.guarantee,
                        product.contact_agent,
                        product.contact_unit,
                        product.dimension_length,
                        product.dimension_width,
                        product.dimension_height,
                        product.dimension_weight
                    );
                };

                const iconA = document.createElement("i");
                iconA.classList.add("fas");
                iconA.classList.add("fa-envelope");
                iconA.classList.add("navitem-icon");

                btnCSV.appendChild(iconA);


                const priceText = document.createElement("div");
                priceText.classList.add("card-text");
                priceText.textContent =
                    "$ " +
                    intlRound(product.regular_price, 2) +
                    " " +
                    product.currency +
                    " / " +
                    product.unit +
                    " " +
                    product.price_tax_status;

                cardText.appendChild(btnModal);
                cardText.appendChild(btnMail);
                cardText.appendChild(btnCSV);

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

    //validate abilities permission for auth model sanctum
    function fetchAbilities() {
        return new Promise((resolve, reject) => {
            fetch(`/profile/abilities`)
                .then(response => response.json())
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    console.error('Error fetching sanctum abilities:', error);
                    reject(error); // Pass error to the calling function
                });

        });
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


    //replace ' for avoid arguments warnings
    function cleanQuotation(text) {
        text.replace(/'/g, '´');
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
    }

    function fetchSample() {
        //alert("Clear cache");
        fetch('/api/sed/cleared', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) { // Check for success message in response
                    window.location.href = '/ppal'; // Redirect to /PPAL on success
                } else {
                    console.error('Error clearing cache:', data.message || 'Unknown error'); // Handle error message
                }
            })
            .catch(error => {
                console.error('Error clearing cache:', error);
            });
    }

    function changeFullImage(image) {
        const mainImage = document.getElementById("prod_full_img");
        mainImage.src = image.src;
        mainImage.alt = image.alt;
    }

    function productCSV() {
        document.getElementById("window-csv").display = "block";
    }

    // pvr willcards start example  for id property. coul by class, etc: const startsAbc = document.querySelectorAll("[id^='abc']");
    // const buttons = document.querySelectorAll('.department-button');
    // buttons.forEach(button => button
    //     .addEventListener('click', () => departmentActions(button)));
