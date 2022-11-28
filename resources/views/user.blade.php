<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <title>CRUD Application with Alpine.js</title>
</head>
<body>
    <div class="container mt-5">
        <div x-data="retrieveUsers()">
            <div class="row">
                <div class="col-4">
                    <div class="card p-3 mt-2">
                        <h3 class="text-center" x-show="isCreate">Create User</h3>
                        <h3 class="text-center" x-show="!isCreate">Update User</h3>
                            <form @submit.prevent="addUser()" x-show="isCreate">
                                <div class="form-group">
                                    <label>Image</label>
                                    <div class="mb-2">
                                      <!-- Show the image -->
                                      <template x-if="imageUrl">
                                        <img :src="imageUrl" 
                                             class="object-cover rounded border border-gray-200" 
                                             style="width: 100px; height: 100px;"
                                        >
                                      </template>

                                      <!-- Show the gray box when image is not available -->
                                      <template x-if="!imageUrl">
                                        <img src="./images/avatar.png" 
                                             class="object-cover rounded border border-gray-200" 
                                             style="width: 100px; height: 100px;"
                                        >
                                      </template>

                                      <!-- Image file selector -->
                                      <input class="mt-2" type="file" accept="image/*" @change="fileChosen" name="image">
                                      <template x-if="errors.image">
                                        <p class="text-danger" x-text="errors.image[0]"></p>
                                    </template>
                                    </div>
                                </div>
        
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" x-model="form.name">
                                    <template x-if="errors.name">
                                        <p class="text-danger" x-text="errors.name[0]"></p>
                                    </template>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" x-model="form.email">
                                    <template x-if="errors.email">
                                        <p class="text-danger" x-text="errors.email[0]"></p>
                                    </template>
                                </div>
                                <button type="submit" class="btn btn-primary">Create</button>
                            </form>
                            <form @submit.prevent="updateUser()" x-show="!isCreate">
                                <div class="form-group">
                                    <label>Image</label>
                                    <div class="mb-2">
                                      <!-- Show the image -->
                                      <template x-if="imageUrl">
                                        <img :src="imageUrl" 
                                             class="object-cover rounded border border-gray-200" 
                                             style="width: 100px; height: 100px;"
                                        >
                                      </template>

                                      <!-- Show the gray box when image is not available -->
                                      <template x-if="!imageUrl">
                                        <img :src="`./storage/${user.image}`" x-model="form.image"
                                             class="object-cover rounded border border-gray-200" 
                                             style="width: 100px; height: 100px;"
                                        >
                                      </template>

                                      <!-- Image file selector -->
                                      <input class="mt-2" type="file" accept="image/*" @change="fileChosen" name="image" x-model="form.email">
                                      <template x-if="errors.image">
                                        <p class="text-danger" x-text="errors.image[0]"></p>
                                    </template>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" x-model="form.name" name="name">
                                    <template x-if="errors.name">
                                        <p class="text-danger" x-text="errors.name[0]"></p>
                                    </template>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" x-model="form.email" name="email">
                                    <template x-if="errors.email">
                                        <p class="text-danger" x-text="errors.email[0]"></p>
                                    </template>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                    </div>
                </div>
                <div class="col-8">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible mt-2">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fas fa-check"></i>
                        {{ session('success') }}
                    </div>
                    @endif
                    <h2>Users List</h2>
                    <button @click="createUser()" class="btn btn-success m-3">Add User</button>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <template x-for="user in users" :key="user.id">
                            <tr>
                                <td x-text="user.id"></td>
                                <td><img :src="`./storage/${user.image}`" alt="" style="width: 100px; height:100px;"></td>
                                <td x-text="user.name"></td>
                                <td x-text="user.email"></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button @click="editUser(user)"
                                            class="btn btn-success">Edit</button>
                                        <button @click="deleteUser(user.id)"
                                            class="btn btn-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>                
            </div>
        </div>
    </div>
</body>
<script>
    function retrieveUsers() {
        return {
            isCreate: true,
            imageUrl: '',
            init(){
                this.getData()
            },
            form: {
                name: '',
                email: '',
                image: ''
            },
            fileChosen(event) {
            this.form.image = event.target.files[0]
            this.fileToDataUrl(event, src => this.imageUrl = src)
            },
            fileToDataUrl(event, callback) {
                  if (! event.target.files.length) return

                  let file = event.target.files[0],
                      reader = new FileReader()

                  reader.readAsDataURL(file)
                  reader.onload = e => callback(e.target.result)
              },
            imageViewer() {
                this.fileChosen(event);
                this.fileToDataUrl(e,c);
            },
            users:[],
            errors:{ },
            getData() {
                axios.get('/api/users')
                    .then((response) => {
                        this.users = response.data;
                    });
            },
            resetError(){
                this.errors.email ='';
                this.errors.name = '';
                this.errors.image = ''
            },
            resetForm(){
                this.form.id = '';
                this.form.name ='';
                this.form.email ='';
                this.imageUrl = '';
            },
            createUser(){
                this.isCreate = true
                this.resetForm();
                this.resetError();                   
            },
            addUser(){
                this.errors = {};
                let formData = new FormData();
                for(let key in this.form){
                    formData.append(key,this.form[key])
                }
                axios.post('/api/users',formData,
                )
                .then((response) => {
                    this.getData();
                    this.resetForm();
                })
                .catch(error => {
                    if (error.response) {
                        let errors = error.response.data.errors;
                        this.errors = errors
                    }
                });
            },
            editUser(user){
                this.isCreate = false
                this.form.id = user.id;
                this.form.name = user.name;
                this.form.email = user.email;
                this.imageUrl = `./storage/${user.image}`
                this.resetError();                   
            },
            updateUser() {
                this.errors = {};
                let formData = new FormData();
                formData.append('name',this.form['name'])
                formData.append('email',this.form['email'])
                formData.append('image',this.form['image'])

                axios.post(`/api/users/${this.form.id}`, formData )
                    .then((response) => {
                        this.getData();
                        this.resetForm();
                    }).catch(error => {
                        if (error.response) {
                            let errors = error.response.data.errors;
                            this.errors = errors
                        }
                    });
            },
            deleteUser(id){
                if (confirm("Are you sure want to delete this user ?")) {
                    axios.delete(`/api/users/${id}`)
                        .then((response) => {
                            this.getData();
                        })
                }
            }
        }
    }

</script>
</html>