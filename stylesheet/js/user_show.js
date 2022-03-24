let groupCard = new Vue({
    el: "#groupCard",
    data: {
        isActive: true,
    },
    methods: {
        active: function () {
            this.isActive = !this.isActive;
        }
    }
})
