package com.healthmanagement.app.navigation

import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import com.healthmanagement.app.data.api.SessionManager
import com.healthmanagement.app.ui.auth.LoginScreen
import com.healthmanagement.app.ui.auth.RegisterScreen
import com.healthmanagement.app.ui.dashboard.DashboardScreen
import com.healthmanagement.app.ui.santri.SantriListScreen
import com.healthmanagement.app.ui.santri.SantriDetailScreen
import com.healthmanagement.app.ui.santri.SantriFormScreen
import com.healthmanagement.app.ui.kelas.KelasScreen
import com.healthmanagement.app.ui.jurusan.JurusanScreen
import com.healthmanagement.app.ui.sickness.SicknessListScreen
import com.healthmanagement.app.ui.sickness.SicknessDetailScreen
import com.healthmanagement.app.ui.sickness.SicknessFormScreen
import com.healthmanagement.app.ui.medicines.MedicineListScreen
import com.healthmanagement.app.ui.medicines.MedicineDetailScreen
import com.healthmanagement.app.ui.medicines.MedicineFormScreen
import com.healthmanagement.app.ui.medicines.MedicineMutationScreen
import com.healthmanagement.app.ui.referral.ReferralListScreen
import com.healthmanagement.app.ui.referral.ReferralFormScreen
import com.healthmanagement.app.ui.report.ReportScreen
import com.healthmanagement.app.ui.settings.SettingsScreen
import com.healthmanagement.app.ui.superadmin.UserManagementScreen
import com.healthmanagement.app.ui.superadmin.UserDetailScreen

@Composable
fun AppNavGraph(navController: NavHostController = rememberNavController()) {
    val isLoggedIn by SessionManager.isLoggedIn.collectAsState(initial = null)

    LaunchedEffect(Unit) {
        SessionManager.initialize()
    }

    if (isLoggedIn == null) return

    val startDestination = if (isLoggedIn == true) Screen.Dashboard.route else Screen.Login.route

    NavHost(navController = navController, startDestination = startDestination) {
        composable(Screen.Login.route) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Screen.Dashboard.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                },
                onNavigateToRegister = { navController.navigate(Screen.Register.route) }
            )
        }
        composable(Screen.Register.route) {
            RegisterScreen(
                onRegisterSuccess = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(Screen.Register.route) { inclusive = true }
                    }
                },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.Dashboard.route) {
            DashboardScreen(
                onNavigateToSantri = { navController.navigate(Screen.SantriList.route) },
                onNavigateToSickness = { navController.navigate(Screen.SicknessList.route) },
                onNavigateToMedicines = { navController.navigate(Screen.MedicineList.route) },
                onNavigateToReferrals = { navController.navigate(Screen.ReferralList.route) },
                onNavigateToReports = { navController.navigate(Screen.Reports.route) },
                onNavigateToSettings = { navController.navigate(Screen.Settings.route) },
                onLogout = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(0) { inclusive = true }
                    }
                }
            )
        }
        composable(Screen.SantriList.route) {
            SantriListScreen(
                onSantriClick = { id -> navController.navigate(Screen.SantriDetail.createRoute(id)) },
                onAddClick = { navController.navigate(Screen.SantriForm.createRoute()) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.SantriDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) {
            val id = it.arguments?.getInt("id") ?: return@composable
            SantriDetailScreen(
                santriId = id,
                onEditClick = { navController.navigate(Screen.SantriForm.createRoute(id)) },
                onBack = { navController.popBackStack() },
                onNavigateToSickness = { sicknessId -> navController.navigate(Screen.SicknessDetail.createRoute(sicknessId)) }
            )
        }
        composable(
            route = Screen.SantriForm.route,
            arguments = listOf(navArgument("id") {
                type = NavType.StringType
                defaultValue = ""
            })
        ) {
            val id = it.arguments?.getString("id")?.toIntOrNull()
            SantriFormScreen(
                santriId = id,
                onSaved = { navController.popBackStack() },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.Kelas.route) {
            KelasScreen(onBack = { navController.popBackStack() })
        }
        composable(Screen.Jurusan.route) {
            JurusanScreen(onBack = { navController.popBackStack() })
        }
        composable(Screen.SicknessList.route) {
            SicknessListScreen(
                onSicknessClick = { id -> navController.navigate(Screen.SicknessDetail.createRoute(id)) },
                onAddClick = { navController.navigate(Screen.SicknessForm.createRoute()) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.SicknessDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) {
            val id = it.arguments?.getInt("id") ?: return@composable
            SicknessDetailScreen(
                sicknessId = id,
                onEditClick = { navController.navigate(Screen.SicknessForm.createRoute(id)) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.SicknessForm.route,
            arguments = listOf(navArgument("id") {
                type = NavType.StringType
                defaultValue = ""
            })
        ) {
            val id = it.arguments?.getString("id")?.toIntOrNull()
            SicknessFormScreen(
                sicknessId = id,
                onSaved = { navController.popBackStack() },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.MedicineList.route) {
            MedicineListScreen(
                onMedicineClick = { id -> navController.navigate(Screen.MedicineDetail.createRoute(id)) },
                onAddClick = { navController.navigate(Screen.MedicineForm.createRoute()) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.MedicineDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) {
            val id = it.arguments?.getInt("id") ?: return@composable
            MedicineDetailScreen(
                medicineId = id,
                onEditClick = { navController.navigate(Screen.MedicineForm.createRoute(id)) },
                onMutationClick = { navController.navigate(Screen.MedicineMutation.createRoute(id)) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.MedicineForm.route,
            arguments = listOf(navArgument("id") {
                type = NavType.StringType
                defaultValue = ""
            })
        ) {
            val id = it.arguments?.getString("id")?.toIntOrNull()
            MedicineFormScreen(
                medicineId = id,
                onSaved = { navController.popBackStack() },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.MedicineMutation.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) {
            val id = it.arguments?.getInt("id") ?: return@composable
            MedicineMutationScreen(
                medicineId = id,
                onSaved = { navController.popBackStack() },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.ReferralList.route) {
            ReferralListScreen(
                onAddClick = { navController.navigate(Screen.ReferralForm.route) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.ReferralForm.route) {
            ReferralFormScreen(
                onSaved = { navController.popBackStack() },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.Reports.route) {
            ReportScreen(onBack = { navController.popBackStack() })
        }
        composable(Screen.Settings.route) {
            SettingsScreen(
                onLogout = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(0) { inclusive = true }
                    }
                },
                onNavigateToUsers = { navController.navigate(Screen.UserManagement.route) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(Screen.UserManagement.route) {
            UserManagementScreen(
                onUserClick = { id -> navController.navigate(Screen.UserDetail.createRoute(id)) },
                onBack = { navController.popBackStack() }
            )
        }
        composable(
            route = Screen.UserDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) {
            val id = it.arguments?.getInt("id") ?: return@composable
            UserDetailScreen(
                userId = id,
                onBack = { navController.popBackStack() }
            )
        }
    }
}
