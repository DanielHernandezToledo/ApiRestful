<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>OAuth Clients</span>
                <a class="action-link" tabindex="-1" @click="showCreateClientForm">
                    Create New Client
                </a>
            </div>

            <div class="card-body">
                <!-- Current Clients -->
                <p class="mb-0" v-if="clients.length === 0">
                    You have not created any OAuth clients.
                </p>

                <table class="table table-responsive table-borderless mb-0" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Name</th>
                            <th>Secret</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients" :key="client.id">
                            <td class="align-middle">{{ client.id }}</td>
                            <td class="align-middle">{{ client.name }}</td>
                            <td class="align-middle">
                                <code>{{ client.secret ? client.secret : '-' }}</code>
                            </td>
                            <td class="align-middle">
                                <a class="action-link" tabindex="-1" @click="edit(client)">
                                    Edit
                                </a>
                            </td>
                            <td class="align-middle">
                                <a class="action-link text-danger" @click="destroy(client)">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Client Modal -->
        <div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog" aria-labelledby="modal-create-client-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-create-client-label">Create Client</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in createForm.errors" :key="error">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="store">
                            <div class="mb-3 row">
                                <label for="create-client-name" class="col-md-3 col-form-label">Name</label>
                                <div class="col-md-9">
                                    <input id="create-client-name" type="text" class="form-control" v-model="createForm.name">
                                    <div class="form-text">Something your users will recognize and trust.</div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="createForm.redirect">
                                    <div class="form-text">Your application's authorization callback URL.</div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-md-3 col-form-label">Confidential</label>
                                <div class="col-md-9">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="confidential" v-model="createForm.confidential">
                                        <label for="confidential" class="form-check-label"></label>
                                    </div>
                                    <div class="form-text">
                                        Require the client to authenticate with a secret. Confidential clients can hold credentials in a secure way without exposing them to unauthorized parties. Public applications, such as native desktop or JavaScript SPA applications, are unable to hold secrets securely.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="store">Create</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog" aria-labelledby="modal-edit-client-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-edit-client-label">Edit Client</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors" :key="error">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="update">
                            <div class="mb-3 row">
                                <label for="edit-client-name" class="col-md-3 col-form-label">Name</label>
                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" class="form-control" v-model="editForm.name">
                                    <div class="form-text">Something your users will recognize and trust.</div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" v-model="editForm.redirect">
                                    <div class="form-text">Your application's authorization callback URL.</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="update">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Secret Modal -->
        <div class="modal fade" id="modal-client-secret" tabindex="-1" role="dialog" aria-labelledby="modal-client-secret-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-client-secret-label">Client Secret</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Here is your new client secret. This is the only time it will be shown so don't lose it! You may now use this secret to make API requests.
                        </p>
                        <input type="text" class="form-control" v-model="clientSecret" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';

    export default {
        data() {
            return {
                clients: [],
                clientSecret: null,
                createForm: {
                    errors: [],
                    name: '',
                    redirect: '',
                    confidential: true
                },
                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                }
            };
        },
        mounted() {
            this.prepareComponent();
        },
        methods: {
            prepareComponent() {
                this.getClients();
                const modalCreate = document.getElementById('modal-create-client');
                const modalEdit = document.getElementById('modal-edit-client');
                modalCreate?.addEventListener('shown.bs.modal', () => {
                    document.getElementById('create-client-name')?.focus();
                });
                modalEdit?.addEventListener('shown.bs.modal', () => {
                    document.getElementById('edit-client-name')?.focus();
                });
            },
            getClients() {
                axios.get('/oauth/clients')
                    .then(response => {
                        this.clients = response.data;
                    });
            },
            showCreateClientForm() {
                const modal = new bootstrap.Modal(document.getElementById('modal-create-client'));
                modal.show();
            },
            store() {
                this.persistClient('post', '/oauth/clients', this.createForm, 'modal-create-client');
            },
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;
                const modal = new bootstrap.Modal(document.getElementById('modal-edit-client'));
                modal.show();
            },
            update() {
                this.persistClient('put', `/oauth/clients/${this.editForm.id}`, this.editForm, 'modal-edit-client');
            },
            persistClient(method, uri, form, modalId) {
                form.errors = [];
                axios[method](uri, form)
                    .then(response => {
                        this.getClients();
                        form.name = '';
                        form.redirect = '';
                        form.errors = [];
                        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        modal.hide();
                        if (response.data.plainSecret) {
                            this.showClientSecret(response.data.plainSecret);
                        }
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = Object.values(error.response.data.errors).flat();
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },
            showClientSecret(clientSecret) {
                this.clientSecret = clientSecret;
                const modal = new bootstrap.Modal(document.getElementById('modal-client-secret'));
                modal.show();
            },
            destroy(client) {
                axios.delete(`/oauth/clients/${client.id}`)
                    .then(() => {
                        this.getClients();
                    });
            }
        }
    };
</script>
