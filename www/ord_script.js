let totalSum = 0;
let currentPage = 1;
const itemsPerPage = 5;
const maxDishes = 5; 

fetchMenu();

function fetchMenu() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;

    fetch('order_conection.php')
        .then(response => response.json())
        .then(data => displayMenu(data.slice(startIndex, endIndex)))
        .catch(error => console.error('Error fetching menu:', error));
}

function displayMenu(menu) {
    const menuContainer = document.getElementById('menu');
    menuContainer.innerHTML = ''; // Clear previous menu items

    menu.forEach(dish => {
        const menuItem = document.createElement('div');
        menuItem.className = 'menu-item';
        menuItem.innerHTML = `<p>${dish.name}</p><p>Cost: ${dish.cost} Kč</p><p> Ingridients : ${dish.recipe}</p> <button data-id="${dish.dishes_id_serial}" data-name="${dish.name}" data-cost="${dish.cost}" class="addToOrderBtn">Add to Order</button>`;
        menuContainer.appendChild(menuItem);
    });

    const addToOrderButtons = document.querySelectorAll('.addToOrderBtn');
    addToOrderButtons.forEach(button => {
        button.addEventListener('click', function () {
            addToOrder(this.dataset.id, this.dataset.name, parseFloat(this.dataset.cost));
        });
    });
}

const prevPageBtn = document.getElementById('prevPageBtn');
const nextPageBtn = document.getElementById('nextPageBtn');

prevPageBtn.addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        fetchMenu();
    }
});
nextPageBtn.addEventListener('click', () => {
    currentPage++;
    fetchMenu();
});

function addToOrder(id, name, cost) {
    const orderContainer = document.getElementById('order');

    // Check if the maximum limit is reached
    if (orderContainer.children.length < maxDishes) {
        const orderItem = document.createElement('div');
        orderItem.name = `${name}`;
        orderItem.id = `${id}`;
        orderItem.className = 'order_item_style';
        orderItem.innerHTML = `<p>${name}</p><p>Cost: ${cost} Kč</p> <button data-id="${id}" data-cost="${cost}" class="discardFromOrderBtn"> Discard from order</button>`;
        orderContainer.appendChild(orderItem);
        totalSum += cost;
        updateTotalSum();

        const discardButton = orderItem.querySelector('.discardFromOrderBtn');
        discardButton.addEventListener('click', function () {
            discardFromOrder(this.dataset.id, parseFloat(this.dataset.cost));
        });
    } else {
        alert('Maximum limit reached. You cannot add more dishes to the order.');
    }
}

function discardFromOrder(id, cost) {
    const orderItem = document.getElementById(`${id}`);
    if (orderItem) {
        orderItem.remove();
        totalSum -= cost;
        updateTotalSum();
    }
}

function updateTotalSum() {
    const totalSumElement = document.getElementById('totalSum');
    totalSumElement.textContent = `Total Sum: ${totalSum} Kč`;
}

const placeOrderBtn = document.getElementById('placeOrderBtn');
placeOrderBtn.addEventListener('click', function () {
    placeOrder();
});

async function placeOrder() {
    const dishIds = Array.from(document.getElementById('order').children).map(item => item.id);
    try {
        const response = await fetch('insert_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ dishIds }),
        });

        if (response.ok) {
            alert('Order placed successfully!');
            location.reload();
        } else {
            console.error('Failed to place order:', response.statusText);
        }
    } catch (error) {
        console.error('Error placing order:', error);
    }
}
