function filterProducts(products, selectedCategories, selectedBrands) {
    return products.filter((product) => {
        const categoryMatch =
            selectedCategories.length === 0 ||
            selectedCategories.includes(product.category);
        const brandMatch =
            selectedBrands.length === 0 ||
            selectedBrands.includes(product.brand);
        return categoryMatch && brandMatch;
    });
}
function sortAndFilterProducts(products, criteria, selectedCategories, selectedBrands) {

    // First filter the products
    let filteredProducts = filterProducts( products, selectedCategories,selectedBrands);

    // Then sort the filtered products
    switch (criteria) {
        case "price-plus":
            filteredProducts.sort((a, b) => b.regular_price - a.regular_price);
            break;
        case "price-less":
            filteredProducts.sort((a, b) => a.regular_price - b.regular_price);
            break;
        case "stock-plus":
            filteredProducts.sort(
                (a, b) => b.stock_quantity - a.stock_quantity
            );
            break;
        case "stock-less":
            filteredProducts.sort(
                (a, b) => a.stock_quantity - b.stock_quantity
            );
            break;
        case "brands":
            filteredProducts.sort((a, b) => a.brand.localeCompare(b.brand));
            break;
    }
    return filteredProducts;
}

function getSelectedFilters() {
    const selectedCategories = Array.from(
        document.querySelectorAll('input[name="cat-array"]:checked')
    ).map((cb) => cb.value);
    const selectedBrands = Array.from(
        document.querySelectorAll('input[name="brand-array"]:checked')
    ).map((cb) => cb.value);
    const orderCriteria = document.getElementById("order-options").value;

    return { selectedCategories, selectedBrands, orderCriteria };
}

function updateProductsDisplay() {
    const { selectedCategories, selectedBrands, orderCriteria } =
        getSelectedFilters();
    // update the title of filters
    const groupElement = document.getElementById("current-group-name");
    const title = groupElement.value;
    let categories = "";
    let brands = "";
    if (selectedCategories.length > 0) {
        categories = ` ${selectedCategories.join("-")}`;
    }
    if (selectedBrands.length > 0) {
        brands = ` ${selectedBrands.join("-")}`;
    }
    //alert('Update Products ' + title + ' '+ categories + ' ' + brands );
    const titleElement = document.getElementById("product-title");
    titleElement.textContent = `${title}${categories}${brands}`;

    const filteredAndSortedProducts = sortAndFilterProducts(
        allProducts,
        orderCriteria,
        selectedCategories,
        selectedBrands
    );
    generateCards(filteredAndSortedProducts);
}

function closeModal(windowForm) {
    var modal = document.getElementById(`${windowForm}`);
    modal.style.display = "none";
}

//formating numbers
function intlRound(number, decimals = 2) {
    if (number === null || number === undefined || number === "") {
        return "";
    }
    const options = {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    };

    const formatter = new Intl.NumberFormat("en-US", options); // Use US English locale for formatting without commas
    return formatter.format(number);
}

//validate strings
function isEmpty(str) {
    return !str || str.trim().length == 0;
}

function getString(text) {
   //return '"' + text + '",';
   if (text.indexOf('\"', 1) >-1 ) text = text.replace('\"', '\'');
   return '"' + text + '",';
}

function getNumber(numeric) {
    return "" + numeric + ",";
}

function productFormCSV(product) {
    var modal = document.getElementById("window-csv");

    const description = document.getElementById("prod_csv_desc");
    description.innerHTML = product.attributes.replaceAll("\r\n", " | ");

    var csvFormat = document.getElementById("prod_csv_noheader");
    csvFormat.innerText =
        getString(product.name) +
        getString(product.sku) +
        getNumber(product.stock_quantity) +
        getNumber(product.regular_price) +
        getString(product.price_tax_status) +
        getString(product.image_1) +
        getString(product.image_2) +
        getString(product.image_3) +
        getString(product.image_4) +
        getString(product.currency) +
        getString(product.description) +
        getString(product.unit) +
        getString(product.department) +
        getString(product.category) +
        getString(product.brand) +
        getString(product.segment) +
        getString(description.innerHTML) +
        getString(product.guarantee) +
        getString(product.contact_agent) +
        getString(product.contact_unit) +
        getNumber(product.dimension_length) +
        getNumber(product.dimension_width) +
        getNumber(product.dimension_height) +
        product.dimension_weight;

    var csvData = document.getElementById("prod_csv");
        csvData.innerText =
        "prod_name,prod_sku,prod_stock,prod_price,prod_tax_status,prod_img_1,prod_img_2,prod_img_3,prod_img_4, " +
        "prod_currency,prod_description,prod_unit,prod_department,prod_category,prod_brand,prod_segment,prod_attributes,prod_guarantee, " +
        "prod_contact,prod_contact_unit,dimension_length,dimension_width,dimension_height,dimension_weight" +
        "\r\n" +
        csvFormat.innerHTML;
        var csvDataHide = document.getElementById("prod_csv_text");
        csvDataHide.value = csvData.innerText;


    description.innerText = "";
    modal.style.display = "block";
}

function temporalIndicator() {
    const cardsContainer = document.getElementById("products-container");
    cardsContainer.innerHTML = "";

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

function ModalData(product) {
    console.log(product.attributes);
    var modal = document.getElementById("window-detail");
    var name = document.getElementById("prod_name");
    name.innerText = product.name;

    var sku = document.getElementById("prod_sku");
    sku.innerText = product.sku;

    //var stock = document.getElementById("prod_stock");
    //stock.innerText = intlRound(product.stock, 0);
    var stock = document.getElementById("prod_stock_1");
    stock.innerText = intlRound(product.stock_quantity, 0);

    var price = document.getElementById("prod_price");
    price.innerText = intlRound(product.regular_price, 2);
    var price = document.getElementById("prod_tax_status");
    price.innerText = product.price_tax_status;

    var prod_img = document.getElementById("prod_full_img");
    prod_img.src = product.image_1;
    prod_img = document.getElementById("prod_img_1");
    prod_img.src = product.image_1;
    prod_img.alt = product.name;

    var image = product.image_2;
    prod_img = document.getElementById("prod_img_2");
    prod_img.src = image;
    prod_img.alt = image.slice(image.lastIndexOf("/") + 1, image.length);

    prod_img = document.getElementById("prod_img_3");
    image = product.image_3;
    prod_img.src = image;
    prod_img.alt = image.slice(image.lastIndexOf("/") + 1, image.length);

    prod_img = document.getElementById("prod_img_4");
    image = product.image_4;
    prod_img.src = image;
    prod_img.alt = image.slice(image.lastIndexOf("/") + 1, image.length);

    var currency = document.getElementById("prod_currency");
    currency.innerText = product.currency;

    var description = document.getElementById("prod_description");
    description.innerText = product.description;
    description = document.getElementById("prod_guarantee");
    description.innerText = product.guarantee;

    // console.log(product.attributes);
    description = document.getElementById("prod_attributes");
    description.innerHTML = product.attributes.replaceAll("\r\n", " | ");
    /*
    var unit = document.getElementById("prod_unit");
    unit.innerText = product.unit;
    */
    var group = document.getElementById("prod_department");
    group.innerText = product.department;
    group = document.getElementById("prod_category");
    group.innerText = product.category;
    group = document.getElementById("prod_brand");
    group.innerText = product.brand;
    group = document.getElementById("prod_segment");
    group.innerText = product.segment;

    var contact = document.getElementById("prod_contact");
    contact.innerText = product.contact_agent;
    contact = document.getElementById("prod_contact_unit");
    contact.innerText = product.contact_unit;

    var dimension = document.getElementById("dimension_length");
    dimension.innerText = intlRound(product.dimension_length, 0);
    dimension = document.getElementById("dimension_width");
    dimension.innerText = intlRound(product.dimension_width, 0);
    dimension = document.getElementById("dimension_height");
    dimension.innerText = intlRound(product.dimension_height, 0);
    dimension = document.getElementById("dimension_weight");
    dimension.innerText = intlRound(product.dimension_weight, 0);

    modal.style.display = "block";
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
            btnModal.onclick = function () {
                ModalData(product);
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
            btnCSV.onclick = function () {
                productFormCSV(product);
            };

            const iconA = document.createElement("i");
            iconA.classList.add("fas");
            iconA.classList.add("fa-file-csv");
            iconA.classList.add("navitem-icon");

            btnCSV.appendChild(iconA);

            const priceText = document.createElement("div");
            priceText.classList.add("card-text");
            priceText.textContent =
                "$ " +
                intlRound(product.regular_price, 2) +
                " " +
                product.currency +
                // " / " +
                // product.unit +
                " " +
                product.price_tax_status;

            cardText.appendChild(btnModal);

            // affinity product data
            if(product.program_url !== null) {
                const btnAffinity = document.createElement("button");
                btnAffinity.onclick = function () {
                    openUrlWindowTab(product.program_url);
                };

                const imageAffinity = document.createElement("img");
                imageAffinity.src = product.program_image;
                imageAffinity.alt = product.brand;
                btnAffinity.appendChild(imageAffinity);

                cardText.appendChild(btnAffinity);
            }

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
            .then((response) => response.json())
            .then((data) => {
                resolve(data);
            })
            .catch((error) => {
                console.error("Error fetching sanctum abilities:", error);
                reject(error); // Pass error to the calling function
            });
    });
}

function changeFullImage(image) {
    const mainImage = document.getElementById("prod_full_img");
    mainImage.src = image.src;
    mainImage.alt = image.alt;
}

function openUrlWindowTab(url) {
    // Open the URL in a new window/tab
    const newWindow = window.open(url, '_blank');

    // Redirect the browser to the new window/tab
    if (newWindow) {
        newWindow.focus();
    } else {
        alert('Please allow pop-ups for this website');
    }
}
