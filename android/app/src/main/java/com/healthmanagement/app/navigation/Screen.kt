package com.healthmanagement.app.navigation

sealed class Screen(val route: String) {
    data object Login : Screen("login")
    data object Register : Screen("register")
    data object Dashboard : Screen("dashboard")
    data object SantriList : Screen("santri")
    data object SantriDetail : Screen("santri/{id}") {
        fun createRoute(id: Int) = "santri/$id"
    }
    data object SantriForm : Screen("santri/form?id={id}") {
        fun createRoute(id: Int? = null) = "santri/form?id=${id ?: ""}"
    }
    data object Kelas : Screen("kelas")
    data object Jurusan : Screen("jurusan")
    data object SicknessList : Screen("sickness")
    data object SicknessDetail : Screen("sickness/{id}") {
        fun createRoute(id: Int) = "sickness/$id"
    }
    data object SicknessForm : Screen("sickness/form?id={id}") {
        fun createRoute(id: Int? = null) = "sickness/form?id=${id ?: ""}"
    }
    data object MedicineList : Screen("medicines")
    data object MedicineDetail : Screen("medicines/{id}") {
        fun createRoute(id: Int) = "medicines/$id"
    }
    data object MedicineForm : Screen("medicines/form?id={id}") {
        fun createRoute(id: Int? = null) = "medicines/form?id=${id ?: ""}"
    }
    data object MedicineMutation : Screen("medicines/mutasi/{id}") {
        fun createRoute(id: Int) = "medicines/mutasi/$id"
    }
    data object ReferralList : Screen("referrals")
    data object ReferralForm : Screen("referrals/form")
    data object Reports : Screen("reports")
    data object Settings : Screen("settings")
    data object UserManagement : Screen("users")
    data object UserDetail : Screen("users/{id}") {
        fun createRoute(id: Int) = "users/$id"
    }
}
