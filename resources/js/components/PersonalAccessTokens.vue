<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Personal Access Tokens</span>
                    <a class="action-link" tabindex="-1" @click="showCreateTokenForm">Create New Token</a>
                </div>

                <div class="card-body">
                    <!-- No Tokens Notice -->
                    <p class="mb-0" v-if="tokens.length === 0">
                        You have not created any personal access tokens.
                    </p>

                    <!-- Personal Access Tokens -->
                    <table class="table table-bordered table-hover mb-0" v-if="tokens.length > 0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="token in tokens" :key="token.id">
                                <td class="align-middle">{{ token.name }}</td>
                                <td class="align-middle">
                                    <a class="action-link text-danger" @click="revoke(token)">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Token Modal -->
        <div class="modal fade" id="modal-create-token" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Token</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <ul>
                                <li v-for="error in form.errors" :key="error">{{ error }}</li>
                            </ul>
                        </div>

                        <!-- Create Token Form -->
                        <form @submit.prevent="store">
                            <div class="mb-3">
                                <label for="create-token-name" class="form-label">Name</label>
                                <input id="create-token-name" type="text" class="form-control" v-model="form.name">
                            </div>

                            <div class="mb-3" v-if="scopes.length > 0">
                                <label class="form-label">Scopes</label>
                                <div v-for="scope in scopes" :key="scope.id" class="form-check">
                                    <input class="form-check-input" type="checkbox" :id="scope.id"
                                        @click="toggleScope(scope.id)" :checked="scopeIsAssigned(scope.id)">
                                    <label class="form-check-label" :for="scope.id">{{ scope.id }}</label>
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

        <!-- Access Token Modal -->
        <div class="modal fade" id="modal-access-token" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Personal Access Token</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Here is your new personal access token. This is the only time it will be shown so don't lose it!
                            You may now use this token to make API requests.
                        </p>
                        <textarea class="form-control" rows="10" readonly>{{ accessToken }}</textarea>
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


export default {
    data() {
        return {
            accessToken: null,
            tokens: [],
            scopes: [],
            form: {
                name: '',
                scopes: [],
                errors: []
            }
        };
    },
    mounted() {
        this.prepareComponent();
    },
    methods: {
        prepareComponent() {
            this.getTokens();
            this.getScopes();

            const createTokenModal = document.getElementById('modal-create-token');
            this.createTokenModalInstance = new Modal(createTokenModal);
        },
        getTokens() {
            axios.get('/oauth/personal-access-tokens').then(response => {
                this.tokens = response.data;
            });
        },
        getScopes() {
            axios.get('/oauth/scopes').then(response => {
                this.scopes = response.data;
            });
        },
        showCreateTokenForm() {
            this.createTokenModalInstance.show();
        },
        store() {
            this.accessToken = null;
            this.form.errors = [];

            axios.post('/oauth/personal-access-tokens', this.form).then(response => {
                this.tokens.push(response.data.token);
                this.showAccessToken(response.data.accessToken);
            }).catch(error => {
                this.form.errors = error.response?.data?.errors || ['Something went wrong.'];
            });
        },
        toggleScope(scope) {
            if (this.scopeIsAssigned(scope)) {
                this.form.scopes = this.form.scopes.filter(s => s !== scope);
            } else {
                this.form.scopes.push(scope);
            }
        },
        scopeIsAssigned(scope) {
            return this.form.scopes.includes(scope);
        },
        showAccessToken(accessToken) {
            this.createTokenModalInstance.hide();
            this.accessToken = accessToken;

            const accessTokenModal = document.getElementById('modal-access-token');
            new Modal(accessTokenModal).show();
        },
        revoke(token) {
            axios.delete(`/oauth/personal-access-tokens/${token.id}`).then(() => {
                this.getTokens();
            });
        }
    }
};
</script>
