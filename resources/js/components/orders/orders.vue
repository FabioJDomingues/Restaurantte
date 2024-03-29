<template>
    <div>
        <v-card>
            <v-card-title class="headline info white--text" primary-title>
                Orders
                <v-spacer></v-spacer>
                <v-text-field @keypress.enter="filterOrders()" v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table
                    :headers="headers"
                    :items="orders"
                    :pagination.sync="pagination"
                    :rows-per-page-items="rowsPerPageItems"
                    :total-items="totalOrders"
                    :loading="loading"
                    item-key="id"
                    class="elevation-1">
                <template slot="items" slot-scope="props">
                    <tr :key="props.item.id"
                        :class="{
                                'in-prep': (props.item.responsible_cook_id === user.id & props.item.state === 'In preparation'),
                                'this-cook-conf': (props.item.responsible_cook_id ===  user.id & props.item.state === 'Confirmed'),
                                'no-cook': (props.item.responsible_cook_id === 0 & user.type === 'cook')
                            }"
                            @click="props.expanded = !props.expanded">
                        <td>
                            <!-- IN PREPARATION BY LOGGED COOK-->
                            <v-chip v-if="(props.item.responsible_cook_id === user.id && props.item.state === 'In preparation')" outline color="blue darken-1">
                                <v-avatar>
                                    <v-icon>timer</v-icon>
                                </v-avatar>
                                <strong>{{ props.item.state }}</strong>
                            </v-chip>

                            <!-- LOGGED COOK CONFIRMED -->
                            <v-chip v-if="(props.item.responsible_cook_id === user.id && props.item.state === 'Confirmed')" outline color="green darken-1">
                                <v-avatar>
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <strong>{{ props.item.state }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                            </v-chip>

                            <!-- CONFIRMED WITH NO COOK -->
                            <v-chip v-if="(user.type === 'cook' & props.item.responsible_cook_id === 0)" outline color="black">
                                <v-avatar>
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <strong>{{ props.item.state }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                            </v-chip>
                            <v-chip v-if="(user.type === 'manager' & props.item.responsible_cook_id === 0)" outline color="red darken-1">
                                <v-avatar>
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <strong>{{ props.item.state }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                            </v-chip>

                            <!-- OTHER COOKS CONFIRMED -->
                            <v-chip v-if="(props.item.responsible_cook_id !== user.id && props.item.responsible_cook_id !== 0)" outline color="red darken-1">
                                <v-avatar>
                                    <v-icon>check_circle</v-icon>
                                </v-avatar>
                                <strong>{{ props.item.state }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                            </v-chip>
                        </td>
                        <td>
                            <strong>{{ props.item.responsible_cook !== '' ? props.item.responsible_cook : ''}}</strong>
                        </td>
                        <td>{{ props.item.created_at }}</td>
                        <td>{{ props.item.start }}</td>
                        <td>{{ props.item.updated_at }}</td>
                        <td class="text-xs-right">
                            <!-- IN PREPARATION BY LOGGED COOK-->
                            <span v-if="props.item.responsible_cook_id === user.id & props.item.state === 'In preparation' & user.type === 'cook'">
                                <v-btn small round color="success"
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'prepared'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Prepared
                                </v-btn>
                            </span>
                            <!-- CONFIRMED BY LOGGED COOK-->
                            <span v-if="props.item.responsible_cook_id === user.id & props.item.state === 'Confirmed' & user.type === 'cook'">
                                <v-btn small round color="primary"
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'in preparation'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Prepare
                                </v-btn>
                                <v-btn small round color="success"
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'prepared'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Prepared
                                </v-btn>
                            </span>
                            <!-- CONFIRMED WITH NO RESPONSIBLE COOK-->
                            <span v-if="props.item.responsible_cook_id === 0 & props.item.state === 'Confirmed' & user.type === 'cook'">
                                <v-btn small round
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'confirmed'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Confirm
                                </v-btn>
                                <v-btn small round color="primary"
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'in preparation'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Prepare
                                </v-btn>
                                <v-btn small round color="success"
                                       :disabled="!user.shift_active"
                                       @click.native="changeOrderState(props.index, props.item, 'prepared'), props.expanded=!props.expanded">
                                    <v-icon></v-icon>
                                    Prepared
                                </v-btn>
                            </span>
                        </td>
                    </tr>
                </template>
                <v-alert slot="no-results" :value="true" color="error" icon="warning">
                    Your search for "{{ search }}" found no results.
                </v-alert>
                <template slot="footer">
                    <td :colspan="headers.length">
                        <strong>Click on an order for details</strong>
                    </td>
                </template>
                <template slot="no-data">
                    <v-alert :value="true" color="info" icon="info">
                        No orders available
                    </v-alert>
                </template>
                <template slot="expand" slot-scope="props">
                    <v-card flat>
                        <v-card-text>
                            <v-list>
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <div slot="activator">
                                            <v-list-tile-title>Order ID</v-list-tile-title>
                                            <v-list-tile-sub-title>{{ props.item.id }}</v-list-tile-sub-title>
                                        </div>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile>
                                    <v-list-tile-content>
                                        <div slot="activator">
                                            <v-avatar size="56px">
                                                <img v-bind:src=props.item.photo_url alt="avatar">
                                            </v-avatar>
                                        </div>
                                    </v-list-tile-content>
                                </v-list-tile>

                                <v-list-tile>
                                    <v-list-tile-content>
                                        <div slot="activator">
                                            <v-list-tile-sub-title>{{ props.item.item }}</v-list-tile-sub-title>
                                        </div>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
    export default  {
        data () {
            return {
                user: {},
                userID: '',
                showPage: false,
                totalOrders: 0,
                orders: [],
                currentOrder: {},
                loading: true,
                pagination: {},
                search: '',
                rowsPerPageItems: [15, 25, 50, 100],
                headers: [
                    { text: 'Status', value: 'state', align: 'left', sortable: false},
                    { text: 'Cook', value: 'responsible_cook'},
                    { text: 'Ordered at', value: 'created_at'},
                    { text: 'Begin', value: 'start'},
                    { text: 'Last update', value: 'updated_at'},
                    { text: '' , value : 'actions' }
                ]
            }
        },
        watch: {
            pagination: {
                handler () {
                    /* if (this.showPage) {
                        this.getDataFromApi().then(data => {
                                this.orders = data.data.orders;
                                this.totalOrders = data.data.totalOrders;
                        });
                    } */
                        this.getDataFromApi();
                    }
                },
                deep: true
        },
        methods: {
            getDataFromApi () {
                this.loading = true;

                axios.get('/api/orders', {
                    params: {
                        page: this.pagination.page, rowsPerPage: this.pagination.rowsPerPage, cookID: this.userID
                    }
                })
                .then((ordersRes) => {
                    this.loading = false;
                    this.orders = ordersRes.data.data
                    this.totalOrders = ordersRes.data.meta.total;
                });
            },
            filterOrders() {
                /* let search = this.search.trim().toLowerCase();

                if (search) {
                    this.orders = this.orders.filter(order => {
                        return Object.values(order)
                            .join(",")
                            .toLowerCase()
                            .includes(search);
                    });
                } */
                this.$toasted.success('TODO: Filter orders',
                    {
                        icon: "info"
                    }
                );
            },
            changeOrderState(index, order, state) {
                this.currentOrder = Object.assign({}, order);

                const orderToUpdate = this.currentOrder;

                // Order already has a cook
                if (order.responsible_cook_id !== 0) {
                    axios.put('/api/orders/' + orderToUpdate.id + '?state=' + state)
                    .then(response => {
                        this.getDataFromApi();

                        this.$toasted.info(response.data.message,
                            {
                                icon: 'info',
                            }
                        );

                        if (state === 'prepared' || state === 'confirmed' || state === 'in preparation') {
                            this.notifyWaiter(order, state);
                        }
                    })
                    .catch((error) => {
                        this.$toasted.error(error,
                            {
                                icon: 'error',
                            }
                        );
                    })
                }
                else { // Order doesn't have a cook
                    axios.put('/api/orders/' + orderToUpdate.id + '?state=' + state + '&responsible_cook_id=' + this.userID)
                    .then(response => {
                        this.getDataFromApi();

                        this.$toasted.info(response.data.message,
                            {
                                icon: 'info',
                            }
                        );

                        if (state === 'prepared' || state === 'confirmed' || state === 'in preparation') {
                            this.notifyWaiter(order, state);
                        }
                    })
                    .catch((error) => {
                        this.$toasted.error(error,
                            {
                                icon: 'error',
                            }
                        );
                    })
                }
            },
            notifyWaiter(order, state) {
                if (order.responsible_cook_id == 0) {
                    order.responsible_cook_id = this.userID
                }

                console.log(order.responsible_cook_id);

                let message = 'Order prepared';
                if (state === 'prepared') {
                    message = 'Order prepared';
                } else if (state == 'confirmed') {
                    message = 'Cook assigned to order';
                } else {
                    message = 'Order in preparation'
                }

                this.$socket.emit('order_prepared', this.user, order);
            },
            changeSort (column) {
                if (this.pagination.sortBy === column)
                {
                    this.pagination.descending = !this.pagination.descending
                }
                else
                {
                    this.pagination.sortBy = column
                    this.pagination.descending = false
                }
            },
            getInformationFromLoggedUser() {
                this.user = this.$store.state.user;
            },
            isUserAWorker(user){
                // TODO: Use vue.js beforeEach() method instead
                if((user.type === 'cook' || user.type === 'manager') && !user.blocked)
                {
                    this.showPage = true;
                    this.userID = user.id;
                }
                else
                {
                    if (user.blocked) {
                        this.$toasted.error('You are not authorized to see this page because your account is blocked',
                            {
                                icon: "error",
                            }
                        );
                    }
                    else {
                        this.$toasted.error('You are not authorized to see this page',
                            {
                                icon: "error",
                            }
                        );
                    }

                    this.$router.push('/menu');
                }
            }
        },
        mounted() {
            this.getInformationFromLoggedUser();
            this.isUserAWorker(this.user);
        },
        sockets: {
            new_order() {
                this.getDataFromApi();
            },
            remove_unfinished_orders() {
                this.getDataFromApi();
            }
        }
    }
</script>

<style scoped>
    .in-prep {
        background-color: #BBDEFB;
    }
    .this-cook-conf {
        background-color: #C8E6C9;
    }
    .other-cook-conf {
        background-color: #FFCDD2;
    }
    .no-cook {
        background-color: #FFECB3;
    }
</style>
