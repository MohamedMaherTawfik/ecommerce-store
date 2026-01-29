<template>
    <section class="py-5" :style="{ backgroundColor: COLORS.BG, color: COLORS.TEXT }">
        <div class="container">
            <!-- Title -->
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3" :style="{ color: COLORS.SECONDARY }">Contact Us</h2>
                <p :style="{ color: COLORS.TEXT_MUTED }">
                    We are here to answer all your questions and help you with anything you need
                </p>
            </div>

            <!-- Card -->
            <div :style="cardStyle" class="card shadow-lg border-0 overflow-hidden">
                <div class="row g-0">

                    <!-- Contact Info -->
                    <div class="col-md-6 p-4 order-md-2"
                        :style="{ backgroundColor: COLORS.PRIMARY, color: COLORS.TEXT }">
                        <h4 class="fw-bold mb-4">Contact Information</h4>

                        <div class="mb-3 d-flex">
                            <i class="bi bi-geo-alt-fill me-3 fs-5"></i>
                            <div>
                                <strong>Address</strong>
                                <p class="mb-0 opacity-75" :style="{ color: rgba(COLORS.TEXT, 0.75) }">Nile Street,
                                    Cairo, Egypt</p>
                            </div>
                        </div>

                        <div class="mb-3 d-flex">
                            <i class="bi bi-telephone-fill me-3 fs-5"></i>
                            <div>
                                <strong>Phone</strong>
                                <p class="mb-0 opacity-75" :style="{ color: rgba(COLORS.TEXT, 0.75) }">+20 123 456 789
                                </p>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <i class="bi bi-envelope-fill me-3 fs-5"></i>
                            <div>
                                <strong>Email</strong>
                                <p class="mb-0 opacity-75" :style="{ color: rgba(COLORS.TEXT, 0.75) }">info@example.com
                                </p>
                            </div>
                        </div>

                        <div>
                            <strong class="d-block mb-2">Follow us</strong>
                            <div class="d-flex gap-2">
                                <a v-for="social in socials" :key="social.name" :href="social.href"
                                    class="btn btn-light btn-sm rounded-circle" :style="socialBtnStyle"
                                    v-html="social.icon"></a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="col-md-6 p-4 order-md-1">
                        <h4 class="fw-bold mb-4" :style="{ color: COLORS.PRIMARY }">Send us a message</h4>

                        <form @submit.prevent="submitForm">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input v-model="form.name" type="text" class="form-control" :style="inputStyle"
                                    required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="form.email" type="email" class="form-control" :style="inputStyle"
                                    required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input v-model="form.subject" type="text" class="form-control" :style="inputStyle"
                                    required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea v-model="form.message" rows="4" class="form-control" :style="inputStyle"
                                    required></textarea>
                            </div>

                            <button type="submit" class="btn w-100" :style="btnStyle">
                                Send Message
                            </button>
                        </form>

                        <div v-if="successMessage" class="alert mt-4" :style="alertStyle">
                            {{ successMessage }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { ref } from "vue";
import COLORS, { rgba } from "@/components/assets/colors.js";

export default {
    name: "Contact",
    setup() {
        const form = ref({
            name: "",
            email: "",
            subject: "",
            message: "",
        });

        const successMessage = ref("");

        const submitForm = () => {
            console.log(form.value);
            successMessage.value = "Your message has been sent successfully!";
            form.value = { name: "", email: "", subject: "", message: "" };
        };

        const socials = [
            {
                name: "facebook",
                href: "#",
                icon: `<i class="bi bi-facebook"></i>`
            },
            {
                name: "twitter",
                href: "#",
                icon: `<i class="bi bi-twitter"></i>`
            },
            {
                name: "instagram",
                href: "#",
                icon: `<i class="bi bi-instagram"></i>`
            },
            {
                name: "telegram",
                href: "#",
                icon: `<i class="bi bi-telegram"></i>`
            },
        ];

        // Styles using COLORS
        const cardStyle = {
            backgroundColor: COLORS.PRIMARY,
            color: COLORS.TEXT,
            borderRadius: "0.5rem",
        };

        const inputStyle = {
            backgroundColor: COLORS.SECONDARY,
            color: COLORS.PRIMARY,
            border: `1px solid ${COLORS.CARD_BORDER}`,
        };

        const btnStyle = {
            backgroundColor: COLORS.ACCENT,
            color: COLORS.BTN_TEXT,
            border: "none",
        };

        const alertStyle = {
            backgroundColor: rgba(COLORS.SUCCESS, 0.15),
            color: COLORS.SUCCESS,
            border: `1px solid ${rgba(COLORS.SUCCESS, 0.3)}`,
        };

        const socialBtnStyle = {
            width: "36px",
            height: "36px",
            borderRadius: "50%",
            border: `1px solid ${rgba(COLORS.TEXT, 0.3)}`,
            display: "inline-flex",
            alignItems: "center",
            justifyContent: "center",
            color: COLORS.TEXT,
            backgroundColor: COLORS.PRIMARY,
        };

        const socialBtnStyleHover = {
            color: COLORS.LINK_HOVER,
            border: `1px solid ${COLORS.LINK_HOVER}`,
        }

        return {
            form,
            submitForm,
            successMessage,
            cardStyle,
            inputStyle,
            btnStyle,
            alertStyle,
            socials,
            socialBtnStyle,
            COLORS,
            rgba
        };
    },
};
</script>
