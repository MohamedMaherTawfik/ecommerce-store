<template>
    <AdminLayout>
        <h1>Admin Panel</h1>
        <div class="dashboard-container">
            <!-- ========== First Row: 4 Cards ========== -->
            <div class="row g-3 mb-4">
                <div class="col-md-3" v-for="card in firstRowCards" :key="card.title">
                    <div class="card stat-card" :class="card.themeClass">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <i :class="['bi', card.icon, 'fs-2']"></i>
                            <div class="text-end">
                                <h4 class="mb-0">{{ card.count }}</h4>
                                <small>{{ card.title }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ========== Second Row: 3 Cards ========== -->
            <div class="row g-3 mb-4">
                <div class="col-md-4" v-for="card in secondRowCards" :key="card.title">
                    <div class="card stat-card" :class="card.themeClass">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <i :class="['bi', card.icon, 'fs-2']"></i>
                            <div class="text-end">
                                <h4 class="mb-0">{{ card.count }}</h4>
                                <small>{{ card.title }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ========== Tables Row ========== -->
            <div class="row g-3">
                <!-- Recent Users -->
                <div class="col-md-6">
                    <div class="card h-100 table-card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Recent Users</span>
                            <a href="#" class="text-decoration-none">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loadingUsers">
                                        <td colspan="5" class="text-center py-4">Loading users...</td>
                                    </tr>
                                    <tr v-else-if="users.length === 0">
                                        <td colspan="5" class="text-center py-4">No users found</td>
                                    </tr>
                                    <tr v-for="user in users" :key="user.id">
                                        <td>{{ user.id }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>{{ user.role }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary me-1"
                                                @click="handleEditUser(user)">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                @click="handleDeleteUser(user.id)"
                                                :disabled="deletingUserId === user.id">
                                                {{ deletingUserId === user.id ? 'Deleting...' : 'Delete' }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Recent Courses -->
                <div class="col-md-6">
                    <div class="card h-100 table-card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Recent Courses</span>
                            <a href="#" class="text-decoration-none">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Teacher</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="course in recentCourses" :key="course.id">
                                        <td>{{ course.title }}</td>
                                        <td>{{ course.teacher }}</td>
                                        <td>{{ course.date }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { toast } from 'vue3-toastify'

// ─── Services ─────────────────────────────────────────────────────────────────
import { getUserCount, getUsers, updateUser, deleteUser } from '@/services/userService'
import { getProductCount } from '@/services/productService'
import { getBrandCount } from '@/services/brandService'
import { getCategoryCount } from '@/services/categoryService'

// ─── State ────────────────────────────────────────────────────────────────────
const isDark = computed(
    () => document.documentElement.getAttribute('data-theme') === 'dark'
)

const userCount = ref(0)
const brandCount = ref(0)
const categoryCount = ref(0)
const productCount = ref(0)

const users = ref([])
const loadingUsers = ref(false)
const deletingUserId = ref(null)

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(async () => {
    loadingUsers.value = true
    try {
        const [uCount, uList, bCount, cCount, pCount] = await Promise.all([
            getUserCount(),
            getUsers(),
            getBrandCount(),
            getCategoryCount(),
            getProductCount(),
        ])
        userCount.value = uCount
        users.value = uList
        brandCount.value = bCount
        categoryCount.value = cCount
        productCount.value = pCount
    } catch (err) {
        console.error('Dashboard init failed:', err)
        toast.error('Failed to load dashboard data')
    } finally {
        loadingUsers.value = false
    }
})

// ─── Handlers ─────────────────────────────────────────────────────────────────
const handleEditUser = async (user) => {
    const newName = prompt('Enter new name:', user.name)
    if (!newName || newName === user.name) return

    try {
        await updateUser(user.id, newName)
        toast.success('User updated successfully')
        const index = users.value.findIndex(u => u.id === user.id)
        if (index !== -1) users.value[index].name = newName
    } catch (err) {
        console.error('Update failed:', err)
        toast.error('Failed to update user')
    }
}

const handleDeleteUser = async (id) => {
    if (!confirm('Are you sure you want to delete this user?')) return

    deletingUserId.value = id
    try {
        await deleteUser(id)
        toast.success('User deleted successfully')
        users.value = users.value.filter(u => u.id !== id)
        userCount.value -= 1
    } catch (err) {
        console.error('Delete failed:', err)
        toast.error('Failed to delete user')
    } finally {
        deletingUserId.value = null
    }
}

// ─── Cards ────────────────────────────────────────────────────────────────────
const firstRowCards = computed(() => [
    { title: 'Users', count: userCount.value, icon: 'bi-people', themeClass: isDark.value ? 'bg-users-dark' : 'bg-users-light' },
    { title: 'Brands', count: brandCount.value, icon: 'bi-award', themeClass: isDark.value ? 'bg-brands-dark' : 'bg-brands-light' },
    { title: 'Categories', count: categoryCount.value, icon: 'bi-tags', themeClass: isDark.value ? 'bg-categories-dark' : 'bg-categories-light' },
    { title: 'Products', count: productCount.value, icon: 'bi-box-seam', themeClass: isDark.value ? 'bg-products-dark' : 'bg-products-light' },
])

const secondRowCards = computed(() => [
    { title: 'Colors', count: 15, icon: 'bi-droplet', themeClass: isDark.value ? 'bg-colors-dark' : 'bg-colors-light' },
    { title: 'Sizes', count: 7, icon: 'bi-arrows-expand', themeClass: isDark.value ? 'bg-sizes-dark' : 'bg-sizes-light' },
    { title: 'Orders', count: 3, icon: 'bi-cart', themeClass: isDark.value ? 'bg-orders-dark' : 'bg-orders-light' },
])

// ─── Dummy Data ───────────────────────────────────────────────────────────────
const recentCourses = ref([
    { id: 1, title: 'ML Course', teacher: 'Admin', date: '1 week ago' },
    { id: 2, title: 'Physics', teacher: 'Jane Smith', date: '2 weeks ago' },
    { id: 3, title: 'Data Structures', teacher: 'Mark Lee', date: '1 month ago' },
])
</script>

<style scoped>
/* ===== Cards ===== */
.stat-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Light theme colors */
[data-theme='light'] .bg-users-light {
    background: #0d6efd !important;
}

[data-theme='light'] .bg-brands-light {
    background: #198754 !important;
}

[data-theme='light'] .bg-categories-light {
    background: #6f42c1 !important;
}

[data-theme='light'] .bg-products-light {
    background: #dc3545 !important;
}

[data-theme='light'] .bg-colors-light {
    background: #0dcaf0 !important;
}

[data-theme='light'] .bg-sizes-light {
    background: #ffc107 !important;
}

[data-theme='light'] .bg-orders-light {
    background: #6c757d !important;
}

/* Dark theme colors */
[data-theme='dark'] .bg-users-dark {
    background: #0a58ca !important;
}

[data-theme='dark'] .bg-brands-dark {
    background: #146c43 !important;
}

[data-theme='dark'] .bg-categories-dark {
    background: #5a32a3 !important;
}

[data-theme='dark'] .bg-products-dark {
    background: #b02a37 !important;
}

[data-theme='dark'] .bg-colors-dark {
    background: #0aa2c0 !important;
}

[data-theme='dark'] .bg-sizes-dark {
    background: #cc9a06 !important;
}

[data-theme='dark'] .bg-orders-dark {
    background: #495057 !important;
}

/* Table Cards */
.table-card {
    border-radius: 12px;
    overflow: hidden;
}

[data-theme='light'] .table-card {
    background: #fff;
    border: 1px solid #dee2e6;
}

[data-theme='dark'] .table-card {
    background: #1e293b;
    border: 1px solid #334155;
}

[data-theme='light'] .card-header {
    background: #f8f9fa;
    color: #212529;
    border-bottom: 1px solid #dee2e6;
}

[data-theme='dark'] .card-header {
    background: #0f172a;
    color: #e2e8f0;
    border-bottom: 1px solid #334155;
}

[data-theme='light'] .table thead th {
    background: #f1f3f5;
    color: #495057;
}

[data-theme='dark'] .table thead th {
    background: #0f172a;
    color: #94a3b8;
}

[data-theme='dark'] .table {
    --bs-table-color: #e2e8f0;
    --bs-table-bg: transparent;
    --bs-table-striped-bg: rgba(30, 41, 59, 0.4);
    --bs-table-hover-bg: rgba(45, 55, 72, 0.6);
}

[data-theme='dark'] .bi {
    opacity: 0.9;
}
</style>
