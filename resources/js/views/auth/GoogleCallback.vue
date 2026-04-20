<script setup>
import { onMounted } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"

const router = useRouter()

onMounted(() => {
    const hash = window.location.hash.substring(1)
    const params = new URLSearchParams(hash)

    const token = params.get("token")
    const role = params.get("role")

    if (token) {
        localStorage.setItem("auth_token", token)
        localStorage.setItem("user_role", role || "user")

        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`

        window.location.replace(role === "admin" ? "/admin" : "/")
    } else {
        router.push("/auth")
    }
})
</script>
