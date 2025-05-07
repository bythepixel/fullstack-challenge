import {ref, computed} from "vue";
import {defineStore} from "pinia";

interface User {
    name: string;
    id: bigint;
    email: string;
    latitude: number; //not sure this should be number, have not dealt with lat/lon in js number before, might be better as string and only use set to prevent coercion
    longitude: number;
    forecast: array;
}

interface DashboardState {
    currentUser: User;
    all: Map<bigint, User>;
}

interface HourlyForecst {
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


export const useDashboardStore = defineStore("dashboard", {
    state: (): DashboardState => {
        return {
            currentUserWeather: [],
            all: new Map(),
            currentUser: User,
        }
    },

    actions: {
        async fetchDashboard() {
            const res = await window.fetch("http://localhost/api/user/dashboard");
            const data = (await res.json())
            await delay()

            let all = new Map<bigint, User>()

            for (const user of data.data) {
                console.log(user)
                all.set(parseInt(user.id), user)
            }
            console.log(all)
            this.all = all
        },
        async fetchUserWeather(user: User) {
            const res = await window.fetch("http://localhost/api/user/" + user.id + '/forecast');
            const data = (await res.json()) as array
            await delay()

            let weather = data.data
            console.log(weather)
            this.currentWeather = weather
            this.currentUser = user
        }
    }

});

export type {User as UserType}