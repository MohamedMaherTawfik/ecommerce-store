<template>
    <AdminLayout>
        <div class="container mt-4">
            <h1>تفاصيل المنتج</h1>

            <div v-if="loading" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
            </div>

            <div v-else-if="error" class="alert alert-danger">
                {{ error }}
            </div>

            <div v-else-if="product" class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ product.name || 'منتج بدون اسم' }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- لو في صورة -->
                            <img v-if="product.image" :src="product.image" alt="صورة المنتج" class="img-fluid rounded"
                                style="max-height: 300px; object-fit: cover;" />
                            <div v-else class="bg-light text-center p-5 rounded">
                                لا توجد صورة
                            </div>
                        </div>

                        <div class="col-md-8">
                            <dl class="row">
                                <dt class="col-sm-3">المعرف</dt>
                                <dd class="col-sm-9">{{ product.id }}</dd>

                                <dt class="col-sm-3">الاسم</dt>
                                <dd class="col-sm-9">{{ product.name }}</dd>

                                <dt class="col-sm-3">السعر</dt>
                                <dd class="col-sm-9">{{ product.price }} جنيه</dd>

                                <dt class="col-sm-3">الكمية</dt>
                                <dd class="col-sm-9">{{ product.quantity || product.stock }}</dd>

                                <dt class="col-sm-3">الوصف</dt>
                                <dd class="col-sm-9">{{ product.description || 'لا يوجد وصف' }}</dd>

                                <dt class="col-sm-3">الفئة</dt>
                                <dd class="col-sm-9">{{ product.category?.name || 'غير محدد' }}</dd>

                                <dt class="col-sm-3">العلامة التجارية</dt>
                                <dd class="col-sm-9">{{ product.brand?.name || 'غير محدد' }}</dd>

                                <dt class="col-sm-3">الحالة</dt>
                                <dd class="col-sm-9">
                                    <span class="badge" :class="product.is_active ? 'bg-success' : 'bg-danger'">
                                        {{ product.is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </dd>
                                <dt class="col-sm-3">تاريخ الإضافة</dt>
                                <dd class="col-sm-9">{{ product.created_at | formatDate }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-warning me-2" @click="editProduct">تعديل</button>
                    <button class="btn btn-danger" @click="deleteProduct">حذف</button>
                </div>
            </div>

            <div v-else class="alert alert-info">
                لم يتم العثور على المنتج
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AdminLayout from '@/layouts/AdminLayout.vue'

// -------------------
const route = useRoute()
const router = useRouter()

const product = ref(null)
const loading = ref(false)
const error = ref(null)

const productId = route.params.id   // ← هنا بنجيب الـ id من الـ URL

onMounted(async () => {
    if (!productId) {
        error.value = 'معرف المنتج غير موجود في الرابط'
        return
    }

    await fetchProduct()
})

const fetchProduct = async () => {
    loading.value = true
    error.value = null

    const token = localStorage.getItem('auth_token')
    if (!token) {
        error.value = 'غير مصرح – يرجى تسجيل الدخول'
        loading.value = false
        return
    }

    try {
        const res = await axios.get(`http://localhost:8000/api/v1/products/${productId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        if (res.data.success) {
            product.value = res.data.data   // ← غيّر حسب شكل الـ response بتاعك
        } else {
            error.value = res.data.message || 'حدث خطأ في جلب البيانات'
        }
    } catch (err) {
        console.error(err)
        error.value = err.response?.data?.message || 'فشل في جلب بيانات المنتج'
    } finally {
        loading.value = false
    }
}

const editProduct = () => {
    router.push(`/admin/products/${productId}/edit`)
}

const deleteProduct = async () => {
    if (!confirm('هل أنت متأكد من حذف المنتج؟')) return

    try {
        const token = localStorage.getItem('auth_token')
        await axios.delete(`http://localhost:8000/api/v1/products/${productId}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        alert('تم حذف المنتج بنجاح')
        router.push('/admin/products')
    } catch (err) {
        alert('فشل في حذف المنتج')
        console.error(err)
    }
}
</script>

<style scoped>
dt {
    font-weight: bold;
    color: #555;
}
</style>
