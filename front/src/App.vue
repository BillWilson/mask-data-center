<template>
    <v-app id="inspire">
<!--        <v-navigation-drawer-->
<!--            v-model="drawer"-->
<!--            app-->
<!--        >-->
<!--            <v-list>-->
<!--                <v-list-item>-->
<!--                    <v-list-item-content>-->
<!--                        <v-list-item-title class="title">口罩地圖</v-list-item-title>-->
<!--                        <v-list-item-subtitle>xoxoxoxoxo</v-list-item-subtitle>-->
<!--                    </v-list-item-content>-->
<!--                </v-list-item>-->
<!--            </v-list>-->

<!--            <v-list dense>-->
<!--                <v-list-item link>-->
<!--                    <v-list-item-action>-->
<!--                        <v-icon>mdi-map-legend</v-icon>-->
<!--                    </v-list-item-action>-->
<!--                    <v-list-item-content @click="routeTo('/')">-->
<!--                        <v-list-item-title>地圖模式</v-list-item-title>-->
<!--                    </v-list-item-content>-->
<!--                </v-list-item>-->
<!--                <v-list-item link>-->
<!--                    <v-list-item-action>-->
<!--                        <v-icon>mdi-file-table-box</v-icon>-->
<!--                    </v-list-item-action>-->
<!--                    <v-list-item-content @click="routeTo('/list')">-->
<!--                        <v-list-item-title>清單模式</v-list-item-title>-->
<!--                    </v-list-item-content>-->
<!--                </v-list-item>-->
<!--            </v-list>-->
<!--        </v-navigation-drawer>-->

        <v-app-bar
            app
            color="indigo"
            dark
        >
<!--            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />-->
            <v-toolbar-title>口罩地圖</v-toolbar-title> (請移動地圖並點擊圖示查閱庫存)
            <v-btn color="blue" small dark
                   href="https://forms.gle/PKP8iMSJ7AdbQEqW7"
                   target="_blank">
                <v-icon left>mdi-alert-octagram</v-icon>
                問題回報
            </v-btn>
        </v-app-bar>

        <v-content>
            <router-view/>
        </v-content>
<!--        <v-footer-->
<!--            color="indigo"-->
<!--            app-->
<!--        >-->
<!--            <span class="white&#45;&#45;text">&copy; 2019</span>-->
<!--        </v-footer>-->
    </v-app>
</template>

<script>
    import router from './router';

    export default {
        props: {
        },
        data: () => ({
            drawer: null,
        }),
        methods: {
            routeTo(path) {
                if (this.$router.currentRoute.path !== path) {
                    router.push({ path: path })
                }
            },
        }
    }
</script>
<style lang="scss">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: #2c3e50;

    &.router-link-exact-active {
      color: #42b983;
    }
  }
}
</style>
