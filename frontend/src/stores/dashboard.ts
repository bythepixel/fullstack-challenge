import {ref, computed} from "vue";
import {defineStore} from "pinia";

interface UserType {
    name: string;
    id: bigint;
    email: string;
    latitude: number; //not sure this should be number, have not dealt with lat/lon in js number before, might be better as string and only use set to prevent coercion
    longitude: number;
    forecast: array<Forecast>;
}

interface DashboardState {
    currentUserWeather: array<Forecast>;
    currentUser: null | UserType;
    all: Map<bigint, UserType>;

}

interface Forecast {
    name: string;
    temperature: string;
    temperatureUnit: string;
    icon: string;
    shortForecast: string;
    detailedForecast: string;
    windSpeed: string;
    windDirection: string;
}

function delay() {
    return new Promise<void>(res => setTimeout(res, 1500))
}


export const useDashboardStore = defineStore<'dashboard', DashboardState,Getters,Actions>("dashboard", {
    state: (): DashboardState => {
        return {
            currentUserWeather: [],
            all: new Map(),
            currentUser: null,
        }
    },

    actions: {
        async fetchDashboard() {
            const res = await window.fetch("http://localhost/api/user/dashboard");
            const data = (await res.json())
            await delay()

            let all = new Map<bigint, UserType>()

            for (const user of data.data) {
                console.log(user)
                all.set(parseInt(user.id), user)
            }
            console.log(all)
            this.all = all
        },
        async fetchUserWeather(user: UserType) {
            const res = await window.fetch("http://localhost/api/user/" + user.id + '/forecast');
            const data = (await res.json()) as array
            await delay()

            let weather = data.data
            console.log(weather)
            this.currentUserWeather = weather
            this.currentUser = user
        },
        clearUser(){
            console.log('trigger')
            this.currentUserWeather = []
            this.currentUser = null
        }
    }

});

export type {UserType as UserType}