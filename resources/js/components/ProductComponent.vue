<template>
    <div class="container">
        <div class="row mt-5">
            <div class="row col-12 my-5">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addNewProduct">Add new Product</button>
            </div>

            <!-- Model form to add new product -->
            <div class="modal fade" id="addNewProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="form_add_product" @submit.prevent="submitNewProduct()">
                                <div class="form-group">
                                    <label for="title" class="col-form-label">Title:</label>
                                    <input type="text" name="title" class="form-control" v-model="product_title" id="title">
                                </div>
                                <div class="form-group">
                                    <label for="price" class="col-form-label">Price:</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="price" v-model="price" id="price">
                                </div>
                                <div class="form-group">
                                    <label for="quantity" class="col-form-label">Quantity:</label>
                                    <input type="number" min="0" class="form-control" name="quantitie" v-model="quantity" id="quantity">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="variant" id="variant" v-model="checkedVariant">
                                    <label class="form-check-label" for="variant">
                                        This product has multiple options, like different sizes or colors
                                    </label>
                                </div>
                                <div class="form-group" v-for="option,key in options" v-if="checkedVariant">
                                    <label for="option_title" class="col-form-label">Option:</label>
                                    <div class="container row">
                                        <input type="text" class="form-control col-6" name="option_title" id="option_title" v-model="option.title">
                                        <div class="col-6 option_value_block">
                                            <span class="list_value" v-for="value,vKey in option.values">{{ value.title }} <i @click="removeOptionValue(key,vKey)" class="fas fa-times"></i></span>
                                            <input type="text" placeholder="Separate options with a comma" class="form-control" name="value_title" v-model="option_value[key]" v-on:keyup="addOptionValue(key, $event)">
                                        </div>
                                        <p :id="`alertForOption_${key}`" class="text-danger col-6 offset-6" style="display: none;"></p>
                                        <a class="text-primary float-right" @click="removeOption(key)" v-if="options.length > 1">Remove</a>
                                    </div>
                                </div>
                                <button class="btn btn-primary" @click.prevent="addOtherOption" v-if="checkedVariant && options.length < 3">Add other</button>
                                <table class="table table-striped mt-3" v-if="variant.length > 0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Variant</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="product,p_key in variant">
                                            <td><span class="option_product" v-for="option,o_key in product.options">{{ option.value }}</span></td>
                                            <td><input  class="form-control" v-model="variant[p_key].price"></input></td>
                                            <td><input  class="form-control" v-model="variant[p_key].quantity"></input></td>
                                            <td><i @click="deleteVariant(p_key)" class="fas fa-trash-alt"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="modal-footer mt-5">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message after addeding new product  -->
            <div class="col-md-8 offset-md-2 alert alert-primary text-center" role="alert" v-if="message">
                {{ message }}
            </div>

            <!-- Showing list of products -->
            <table class="table table-striped" v-if="products.length > 0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Variant</th>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product,key in products">
                        <th scope="row">{{ product.id }}</th>
                        <td>{{ product.title }}</td>
                        <td>{{ product.price }}</td>
                        <td>{{ product.quantitie }}</td>
                        <td>{{ product.variant ? 'Yes' : 'No' }}</td>
                        <td v-if="product.variants.length > 0">
                            <button class='btn btn-primary' data-toggle="modal" :data-target="`#variantsProduct_${key}`">Variants</button>
                            <div class="modal fade" :id="`variantsProduct_${key}`" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Product variants</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-striped mt-3">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Variant</th>
                                                        <th scope="col">Price</th>
                                                        <th scope="col">Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="variant,key in product.variants">
                                                        <td><span class="option_product" v-for="option,o_key in variant.options">{{ option.value }}</span></td>
                                                        <td>{{ variant.price }}</td>
                                                        <td>{{ variant.quantitie }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer mt-5">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- message appeare if there is no product -->
            <div class="col-md-8 offset-md-2 alert alert-primary text-center" role="alert" v-else>
                There's no product for now!
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        created() {
            this.init()
        },
        mounted() {
            console.log('Component mounted.')
            this.flashMessage()
        },
        data() {
            return {
                products: [],
                product_title: '',
                price: 0,
                quantity: 0,
                message: '',
                checkedVariant: false,
                option_value: [],
                options: [{ 
                    title: '',
                    values: [],
                }],
                variant: []
            }
        },
        methods: {
            init() {
                axios.get(`/product/get`)
                        .then(res => {
                            this.products = res.data.products
                        })
                        .catch(err => {});
            },
            submitNewProduct() {
                let formData = ''
                let form = document.getElementById('form_add_product')

                if(this.checkedVariant){
                    formData = {
                        title: this.product_title,
                        price: this.price,
                        quantity: this.quantity,
                        products: this.variant,
                        checkedVariant: this.checkedVariant
                    }
                }else{
                    formData = new FormData(form)
                }

                axios.post('/product/add', formData)
                    .then(res => {
                        if(res.data.state){
                            $('#addNewProduct').modal('hide');
                            form.reset()
                            this.options = [{ 
                                title: '',
                                values: [],
                            }]
                            this.variant = []
                            this.checkedVariant = false                
                            this.product_title = ''
                            this.price = 0
                            this.quantity = 0
                            this.message = res.data.message
                            this.init()
                        }
                    })
                    .catch(err => {});
            },
            addOptionValue: function (key, event) {
                if (event) {
                    if((event.keyCode == 13 || event.keyCode == 188) && this.option_value[key].length > 0){

                        let value = this.option_value[key].split(',')
                        let alert = document.getElementById('alertForOption_'+key)

                        let search_value = this.options[key].values.filter(function (el) {
                            return el.title.toLowerCase() == value[0].toLowerCase()
                        })

                        if(search_value.length > 0 && this.options[key].values.length != 0){
                            alert.style.display = 'block'
                            alert.textContent = "This value already exist!"
                        }else{
                            alert.style.display = 'none'
                            this.options[key].values.push({
                                title: value[0],
                            })
                        }

                        this.option_value[key] = ''

                    }else if(event.keyCode == 8 && this.option_value[key].length == 0) {
                        this.options[key].values.splice(this.options[key].values.length-1, 1)
                    }
                }
                this.generateVariant()
            },
            addOtherOption() {
                this.options.push({
                    title: '',
                    values: [],
                })
            },
            removeOption(key) {
                this.options.splice(key, 1)
                this.generateVariant()
            },
            removeOptionValue(key, vKey){
                this.options[key].values.splice(vKey, 1)
                this.generateVariant()
            },
            deleteVariant(key){
                this.variant.splice(key, 1)
                if(this.variant.length == 0){
                    this.variant = []
                }
            },
            generateVariant() {
                let attributes = this.options

                let getProducts = (arrays) => {
                    if (arrays.length === 0) {
                        return [[]]
                    }

                    let results = []

                    getProducts(arrays.slice(1)).forEach((product) => {
                        arrays[0].forEach((value) => {
                            results.push([value].concat(product))
                        })
                    })

                    return results
                };

                let getAllCombinations = (attributes) => {
                            
                    let attributeNames = []
                    let attributeValues = []

                    for(let i = 0; i < attributes.length; i++){
                        attributeNames.push(attributes[i].title)
                        attributeValues.push(attributes[i].values)
                    }

                    let price = this.price
                    let quantity = this.quantity

                    return getProducts(attributeValues).map((product) => {
                        let obj = {
                            price: price,
                            quantity: quantity,
                            options: []
                        }
                        
                        attributeNames.forEach((name, i) => {
                            obj['options'].push({
                                title: name,
                                value: product[i].title
                            }
                            )
                        })
                        
                        return obj
                    })
                }

                this.variant = getAllCombinations(attributes)
            },
            flashMessage() {
                setInterval(() => (this.message = ''), 30000)
            }
        }
    }
</script>
