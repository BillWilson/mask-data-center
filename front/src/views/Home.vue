<template>
    <l-map
        class="fill-height"
        :zoom="zoom"
        :center="center"
        :options="mapOptions"
        style="width: 100%; "
        @update:center="centerUpdate"
        @update:zoom="zoomUpdate"
    >
        <l-tile-layer
            :url="url"
            :attribution="attribution"
        />
        <l-control position="bottomleft" >
            <v-btn>宣導資訊</v-btn>
        </l-control>

        <l-marker v-for="(item) in this.list" :key="item.code" :lat-lng="item.posPoint">
            <l-popup style="width: 300px">
                <div>
                    <v-card>
                        <v-card-title>{{item.name}}</v-card-title>

                        <v-card-text>
                            <div>{{item.address}}</div>
                            <div>{{item.tel}}</div>
                            <div><a :href="'https://www.google.com.tw/maps/search/' + item.address" target="_blank">Google Map</a></div>
                        </v-card-text>

                        <v-divider class="mx-4"></v-divider>

                        <v-card-title>目前庫存</v-card-title>

                        <v-card-text>
                            <v-chip-group
                                active-class="deep-purple accent-4 white--text"
                                column
                            >
                                <v-chip
                                    color="indigo"
                                    text-color="white">
                                    <v-avatar left>
                                        <v-icon>mdi-account-circle</v-icon>
                                    </v-avatar>
                                    成人口罩總剩餘數: {{item.adult}}
                                </v-chip>

                                <v-chip
                                    color="primary"
                                    text-color="white">
                                    <v-avatar left>
                                        <v-icon>mdi-account-circle</v-icon>
                                    </v-avatar>
                                    兒童口罩剩餘數: {{item.child}}
                                </v-chip>
                            </v-chip-group>
                            <p class="font-weight-black">上次更新: {{ item.updated_at | moment("from") }}</p>
                        </v-card-text>
                    </v-card>
                </div>
            </l-popup>
        </l-marker>

    </l-map>
</template>

<script>
    // @ is an alias to /src
    import { latLng } from "leaflet";
    import { LMap, LTileLayer, LMarker, LPopup,  LControl } from "vue2-leaflet";

    export default {
        name: 'home',
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LPopup,
            LControl,
        },
        data () {
            return {
                zoom: 8,
                center: latLng(23.982664624813566, 121.01411378997452),
                url: "http://{s}.tile.osm.org/{z}/{x}/{y}.png",
                attribution:
                    '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                withPopup: latLng(23.982664624813566, 121.01411378997452),
                withTooltip: latLng(23.982664624813566, 121.01411378997452),
                currentZoom: 11.5,
                currentCenter: latLng(23.982664624813566, 121.01411378997452),
                showParagraph: true,
                mapOptions: {
                    zoomSnap: 0.5
                },
                list: [],
            }
        },
        mounted: function() {
            this.getList();
        },
        methods: {
            getList() {
                // const range = this.currentZoom;

                this.$http.get('http://localhost:8001/api/facilities?latitude='+this.currentCenter.lat+'&longitude='+this.currentCenter.lng+'&radius=2500').then((response) => {
                    this.list = response.data.data.map((item) => {
                        item.posPoint = latLng(item.latitude, item.longitude);
                        window.console.log(this.list);
                        return item;
                    });

                })
            },
            zoomUpdate(zoom) {
                window.console.log(zoom);
                this.currentZoom = zoom;
            },
            centerUpdate(center) {
                window.console.log(center);
                this.currentCenter = center;
                this.getList();
            },
        }
    }
</script>
