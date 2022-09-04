<script setup>
    import { onMounted, ref, reactive } from 'vue';

    let carts = ref([])
    let total = ref(0)
    let cartItems = ref(0)
    let couponCode = ref("")
    let couponDiscount = ref(0)
    const cartData = reactive({
        id: 0,
        quantity: 0,
        product_id: 0,
        type: ''
    })

    onMounted(() => {
        getCartProducts(),
        getCartItems()
    })

    // Get all products
    const getCartProducts = () => { 
        axios.get('api/get-cart-products')
            .then(response => {
                carts.value = response.data.cartItems;
                total.value = response.data.totalAmount;
                // console.log(total);
            }) 
        };
    
    //increase product quantity an price
    const UpdateAmountPlus = (id, quantity, product_id) => {
        cartData.id = id;
        cartData.quantity = quantity;
        cartData.product_id = product_id;
        cartData.type = "plus";
        axios.post('api/update-cart', cartData);
        getCartProducts();
        getCartItems();
    }

    //increase product quantity an price
    const UpdateAmountMinus = (id, quantity, product_id) => {
        if (quantity != 1) {
            cartData.id = id;
            cartData.quantity = quantity;
            cartData.product_id = product_id;
            cartData.type = "minus";
            axios.post('api/update-cart', cartData);
            getCartProducts();
            getCartItems();
        }else
        {
            alert("Product quantity can't less than 1");
        }        
    };

    //Get total number of items
    const getCartItems = () =>{
        axios.get('api/cart-items')
        .then(response => {
            cartItems.value = response.data.items;
        })
    }

    //get coupon code
    const getCouponCode = () =>{
        axios.get('api/apply-coupon/'+ couponCode.value)
            .then(response => {
                if(response.data.discount){
                couponDiscount.value = response.data.discount;
                alert(response.data.message);
                couponCode.value = null;
                }else{
                    alert(response.data.message);
                }
            })
    }

</script>

<template>
    <div class="container py-3">
        <h2 class="text-center mb-3" ><strong>SHOPPING CART</strong></h2>
        <button class="btn btn-outline-dark" style="float:right">
            <i class="bi-cart-fill me-1"></i>
            Items
            <span class="badge bg-dark text-white ms-1 rounded-pill">{{ cartItems }}</span>
        </button>
        <table class="table table-responsive">
            <thead>
                <tr>
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Qantity</th>
                <th scope="col">Unite Price</th>
                <th scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in carts" :key="product.id" >
                    <td>
                        <div style="width:100px;">
                            <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />                
                        </div>
                    </td>
                    <td>{{ product.product.product_name }}</td>
                    <td>
                        <button type="button" class="btn" @click="UpdateAmountMinus(product.id, product.qty, product.product_id)" style="font-size:30px;">-</button>
                        <input style="max-width:80px" v-model="product.qty" type="number" readonly>
                        <button type="button" class="btn" @click="UpdateAmountPlus(product.id, product.qty, product.product_id)" style="font-size:30px;">+</button>
                    </td>
                    <td>#{{ product.product.product_price }}</td>
                    <td>#{{ product.product.product_price * product.qty }}</td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6 my-5">
                        <form @submit.prevent="getCouponCode()">                    
                            <input type="text" placeholder="Coupon Code" v-model="couponCode">                    
                            <button class="btn btn-success text-dark" type="submit">Go</button>                    
                        </form>
                    </div>
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="row my-3">
                    <div class="col-6">
                        Total:
                    </div>
                    <div class="col-6">
                        #{{ total }}
                    </div>
                </div>
                <hr>
                <div class="row my-3">
                    <div class="col-6">
                        Discout Price:
                    </div>
                    <div class="col-6">
                        #{{ couponDiscount }}
                    </div>
                </div>
                <hr>
                <div class="row my-3">
                    <div class="col-6">
                        Grand Total:
                    </div>
                    <div class="col-6">
                        #{{ total - couponDiscount }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5"></div>
                <div class="col-md-7">
                    <button class="btn btn-success btn-sm w-25">Check Out</button>   
                </div>                
            </div>
        </div>                
    </div>
</template>