<template>
    <div>
        <section class="py-3">
            <div class="container px-4 px-lg-5 mt-5">
                <form @submit.prevent="createProduct">
                    <div class="row mb-4 col-md-8 offset-md-2 col-sm-12">
                        <div class="col-md-6">
                            <div class="form-outline">
                                <label class="form-label" for="productName">Product Name</label>
                                <input v-model="data.name" type="text" id="productName" :class="validateField('name')" class="form-control" />
                                <small v-for="error in errors.name" v-if="validateField('name')" class="text-danger">
                                    {{ error }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-outline">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input v-model="data.quantity" type="text" id="quantity" :class="validateField('quantity')" class="form-control" />
                                <small v-for="error in errors.quantity" v-if="validateField('quantity')" class="text-danger">
                                    {{ error }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <div class="form-outline mb-4">
                                <label class="form-label" for="unit_price">Unit Price</label>
                                <input v-model="data.unit_price" type="text" id="unit_price" :class="validateField('unit_price')" class="form-control" />
                                <small v-for="error in errors.unit_price" v-if="validateField('unit_price')" class="text-danger">
                                    {{ error }}
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6 mt-2">

                            <div class="form-outline mb-4">
                                <div v-if="busy" class="mt-4 spinner-border" role="status"></div>
                                <button v-else type="submit" class="btn btn-primary btn-block mt-4"><span v-if="current_id === null">Submit</span> <span v-else>Update</span></button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>

        </section>

        <section v-if="products.length > 0" class="py-3 container">
            <div class="table-responsive table-bordered">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Quantity in Stock</th>
                        <th scope="col">Price Per Item</th>
                        <th scope="col">Datetime Submitted</th>
                        <th scope="col">Total Value Number</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="product in products">
                        <td>{{ product.name}}</td>
                        <td>{{  product.quantity }}</td>
                        <td>{{ product.unit_price }}</td>
                        <td>{{ product.created_at }}</td>
                        <td>{{ product.quantity * product.unit_price }}</td>
                        <td><button @click="editProduct(product)" class="btn btn-info">Edit</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li @click="getProducts(current_page -1)" :class="{disabled :current_page == 1}" class="page-item"><a class="page-link " href="#">Previous</a></li>
                    <li @click="getProducts(item)" v-for="item in pagination" :class="{disabled :(current_page ===item || item === '...')}" class="page-item"><a class="page-link" href="#">{{ item }}</a></li>
                    <li @click="getProducts(current_page + 1)" :class="{disabled : current_page === last_page}" class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </section>

    </div>
</template>

<script>
export default {
    name: "Products",
    data() {
        return {
            busy: false,
            data: {
                name: '',
                quantity: '',
                unit_price: '',
            },
            errors: {
                name: '',
                quantity: '',
                unit_price: '',
            },
            products: [],
            current_page: 1,
            last_page: '',
            pagination: {},
            current_id: null

        }
    },

    methods: {
        editProduct(product){
            this.clearErrors()
            this.data = {
                name: product.name,
                unit_price: product.unit_price,
                quantity: product.quantity
            }
            this.current_id = product.id
        },
        paginationMethod(c, m){
            var current = c,
                last = m,
                delta = 2,
                left = current - delta,
                right = current + delta + 1,
                range = [],
                rangeWithDots = [],
                l;

            for (let i = 1; i <= last; i++) {
                if (i == 1 || i == last || i >= left && i < right) {
                    range.push(i);
                }
            }

            for (let i of range) {
                if (l) {
                    if (i - l === 2) {
                        rangeWithDots.push(l + 1);
                    } else if (i - l !== 1) {
                        rangeWithDots.push('...');
                    }
                }
                rangeWithDots.push(i);
                l = i;
            }

            console.log(rangeWithDots)
            this.pagination = rangeWithDots;
            return rangeWithDots;
        },
        clearErrors(){
            this.errors = {
                name: '',
                unit_price: '',
                quantity: ''
            }
        },
        clearData(){
            this.data = {
                name: '',
                unit_price: '',
                quantity: ''
            }
        },
        updateProduct(){
            this.busy = false
            this.clearErrors()
            axios.patch('/api/products/' + this.current_id, this.data).then((resp) => {

                let prod_index = this.products.map((product) => {return product.id}).indexOf(this.current_id)
                if(prod_index !== -1){
                    this.products[prod_index] = resp.data
                }

                this.clearData()
                this.busy = false
            }).catch((error) => {
                console.log(error.response.data.errors)
                let error_message = error.response.data.errors;
                for(const key in error_message){
                    this.errors[key] = error_message[key]
                }
                this.busy = false

            })
        },
        createProduct(){
            if(this.current_id !== null){
                return this.updateProduct()
            }
            this.busy = true
            this.clearErrors()
            axios.post('/api/products', this.data).then((resp) => {
                this.products.unshift(resp.data)
                this.clearData()
                this.busy = false
            }).catch((error) => {
                console.log(error.response.data.errors)
                let error_message = error.response.data.errors;
                for(const key in error_message){
                    this.errors[key] = error_message[key]
                }
                this.busy = false

            })
        },
        getProducts(page_number){
            axios.get('/api/products/pages/' + page_number).then((resp) => {
                if(resp.data.data.length > 0){
                    this.products = resp.data.data
                    this.current_page = resp.data.current_page
                    this.last_page = resp.data.last_page
                    this.paginationMethod(resp.data.current_page, resp.data.last_page)
                }
            }).catch((error) => {

            })
        },
        validateField(value){
            if(value === 'quantity')
                return this.errors.quantity.length > 0? 'is-invalid': ''
            else if(value === 'name')
                return this.errors.quantity.length > 0? 'is-invalid': ''
            else if(value === 'unit_price')
                return this.errors.quantity.length > 0? 'is-invalid': ''
        }
    },
    created(){
        this.getProducts(1)
    }
}
</script>

<style scoped>

</style>
