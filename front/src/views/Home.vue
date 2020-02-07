import {propsBinder} from "vue2-leaflet";import {findRealParent} from "vue2-leaflet";
<template>
    <l-map
        class="fill-height"
        :zoom="zoom"
        :center="center"
        :options="mapOptions"
        style="width: 100%; "
        @update:center="centerUpdate"
        @update:zoom="zoomUpdate"
        @ready="onReady"
        @locationfound="onLocationFound"
    >
        <l-tile-layer
            :url="url"
            :attribution="attribution"
        />
        <v-locatecontrol/>
        <l-control position="bottomleft">
            <v-btn color="pink" dark
                   href="https://www.facebook.com/mohw.gov.tw/photos/a.484593545040402/1472260732940340/?type=1&theater"
                   target="_blank">
                <v-icon left>mdi-star</v-icon>
                宣導資訊
            </v-btn>
        </l-control>

        <l-marker v-for="(item) in this.list" :key="item.code" :lat-lng="item.posPoint" style="width: 30vh">
            <l-popup  autoPan autoClose>
                <div>
                    <v-card
                        class="mx-auto"
                        max-width="400"
                        :flat="true"
                    >
                        <v-list-item two-line>
                            <v-list-item-content>
                                <v-list-item-title class="headline font-weight-bold">{{item.name}}
                                    <v-btn class="ma-2" tile large color="teal" small icon :href="'https://www.google.com.tw/maps/search/' + item.address" target="_blank">
                                        <v-icon>mdi-call-split</v-icon>
                                    </v-btn>
                                </v-list-item-title>
                                <v-list-item-subtitle>{{item.address}}</v-list-item-subtitle>
                                <v-list-item-subtitle><a :href="'tel:' + item.tel" target="_blank">{{item.tel}}</a></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item>
                            <v-chip-group
                                active-class="deep-purple accent-4 white--text"
                                column
                            >
                                <v-alert
                                    border="right"
                                    colored-border
                                    type="error"
                                    elevation="2"
                                >
                                    因部份藥局採用發放號碼牌方式，實際庫存請依照藥局現場為準。
                                </v-alert>

                                <v-chip
                                    color="indigo"
                                    text-color="white">
                                    <v-avatar left>
                                        <v-icon>mdi-human-male-female</v-icon>
                                    </v-avatar>
                                    成人口罩總剩餘數: {{item.adult}}
                                </v-chip>

                                <v-chip
                                    color="primary"
                                    text-color="white">
                                    <v-avatar left>
                                        <v-icon>mdi-human-child</v-icon>
                                    </v-avatar>
                                    兒童口罩剩餘數: {{item.child}}
                                </v-chip>

                            </v-chip-group>
                        </v-list-item>

                        <v-btn text>上次更新: {{ item.updated_at | moment("from") }}</v-btn>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-btn text>資料來源： 衛生福利部健保資料庫</v-btn>
                        </v-card-actions>
                    </v-card>
                </div>
            </l-popup>
        </l-marker>

    </l-map>
</template>

<script>
    // @ is an alias to /src
    import {DomEvent, latLng} from "leaflet";
    import {LMap, LTileLayer, LMarker, LPopup, LControl} from "vue2-leaflet";
    import Vue2LeafletLocatecontrol from 'vue2-leaflet-locatecontrol/Vue2LeafletLocatecontrol'

    export default {
        name: 'home',
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LPopup,
            LControl,
            'v-locatecontrol': Vue2LeafletLocatecontrol
        },
        data() {
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
        mounted: function () {
            this.getList();
        },
        methods: {
            getList() {
                // const range = this.currentZoom;

                this.$http.get('http://localhost:8001/api/facilities?latitude=' + this.currentCenter.lat + '&longitude=' + this.currentCenter.lng + '&radius=2500').then((response) => {
                    this.list = response.data.data.map((item) => {
                        item.posPoint = latLng(item.latitude, item.longitude);
                        return item;
                    });

                })
            },
            zoomUpdate(zoom) {
                this.currentZoom = zoom;
            },
            centerUpdate(center) {
                this.currentCenter = center;
                this.getList();
            },
            onReady (mapObject) {
                mapObject.locate();
            },
            onLocationFound(location){
                this.currentCenter = location.latlng;
                // this.$refs.map.mapObject.
            }
        }
    }
</script>

<style scoped>
    @import "~leaflet/dist/leaflet.css";
    @import "https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css";
</style>
