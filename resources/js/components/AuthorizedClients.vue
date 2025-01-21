<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div v-if="tokens.length > 0">
            <div class="card">
                <div class="card-header">Authorized Applications</div>

                <div class="card-body">
                    <!-- Authorized Tokens -->
                    <table class="table table-responsive table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Scopes</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens" :key="token.id">
                                <!-- Client Name -->
                                <td class="align-middle">
                                    {{ token.client.name }}
                                </td>

                                <!-- Scopes -->
                                <td class="align-middle">
                                    <span v-if="token.scopes.length > 0">
                                        {{ token.scopes.join(', ') }}
                                    </span>
                                </td>

                                <!-- Revoke Button -->
                                <td class="align-middle">
                                    <a class="action-link text-danger" @click="revoke(token)">
                                        Revoke
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tokens: []
            };
        },

        mounted() {
            this.prepareComponent();
        },

        methods: {
            prepareComponent() {
                this.getTokens();
            },

            getTokens() {
                axios.get('/oauth/tokens')
                    .then(response => {
                        this.tokens = response.data;
                    });
            },

            revoke(token) {
                axios.delete(`/oauth/tokens/${token.id}`)
                    .then(() => {
                        this.getTokens();
                    });
            }
        }
    };
</script>
