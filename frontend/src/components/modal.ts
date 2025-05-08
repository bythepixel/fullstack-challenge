import { ref, shallowRef } from "vue";
import User from "@/components/User.vue";
import type {UserType} from "@/stores/dashboard";


const show = ref(false);
const component = shallowRef();

export function useModal() {
    return {
        show,
        component,
        showModal: (type: 'user', user: UserType) => {
            show.value = true
            switch (type) {
                case 'user': return component.value = User
            }
        },
        hideModal: () => (show.value = false),
    };
}