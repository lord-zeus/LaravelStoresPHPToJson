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
                                <button type="submit" class="btn btn-primary btn-block mt-4">Sign up</button>

                            </div>
                        </div>

                    </div>
                </form>

            </div>

        </section>

    </div>
</template>

<script>
export default {
    name: "Products",
    data() {
        return {
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
        }
    },

    methods: {
        clearErrors(){
            this.errors = {
                name: '',
                unit_price: '',
                quantity: ''
            }
        },
        createProduct(){
            this.clearErrors()
            axios.post('/api/products', this.data).then((resp) => {

            }).catch((error) => {
                console.log(error.response.data.errors)
                let error_message = error.response.data.errors;
                for(const key in error_message){
                    this.errors[key] = error_message[key]
                }
            })
        },
        getProducts(page_number){
            axios.get('/api/products').then((resp) => {
                console.dir(resp.data)
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
