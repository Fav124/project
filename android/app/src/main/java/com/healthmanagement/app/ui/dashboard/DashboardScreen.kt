package com.healthmanagement.app.ui.dashboard

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.model.DashboardSummary
import com.healthmanagement.app.ui.components.*
import com.healthmanagement.app.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    onNavigateToSantri: () -> Unit,
    onNavigateToSickness: () -> Unit,
    onNavigateToMedicines: () -> Unit,
    onNavigateToReferrals: () -> Unit,
    onNavigateToReports: () -> Unit,
    onNavigateToSettings: () -> Unit,
    onLogout: () -> Unit,
    viewModel: DashboardViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    var drawerOpen by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) { viewModel.loadDashboard() }
    LaunchedEffect(state.loggedOut) { if (state.loggedOut) onLogout() }

    ModalNavigationDrawer(
        drawerState = rememberDrawerState(initialValue = DrawerValue.Closed).also {
            LaunchedEffect(drawerOpen) { if (drawerOpen) it.open() else it.close() }
        },
        gesturesEnabled = drawerOpen,
        drawerContent = {
            ModalDrawerSheet {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "SIM Kesantrian",
                    style = MaterialTheme.typography.titleLarge,
                    fontWeight = FontWeight.Bold,
                    color = MaterialTheme.colorScheme.primary,
                    modifier = Modifier.padding(16.dp)
                )
                HorizontalDivider()
                DrawerItem(Icons.Default.People, "Santri", onClick = { drawerOpen = false; onNavigateToSantri() })
                DrawerItem(Icons.Default.MedicalServices, "Santri Sakit", onClick = { drawerOpen = false; onNavigateToSickness() })
                DrawerItem(Icons.Default.Medication, "Obat", onClick = { drawerOpen = false; onNavigateToMedicines() })
                DrawerItem(Icons.Default.LocalHospital, "Rujukan RS", onClick = { drawerOpen = false; onNavigateToReferrals() })
                DrawerItem(Icons.Default.BarChart, "Laporan", onClick = { drawerOpen = false; onNavigateToReports() })
                HorizontalDivider()
                DrawerItem(Icons.Default.Settings, "Pengaturan", onClick = { drawerOpen = false; onNavigateToSettings() })
                DrawerItem(Icons.Default.Logout, "Keluar", onClick = { drawerOpen = false; viewModel.logout() })
            }
        }
    ) {
        AppScaffold(
            title = "Dashboard",
            navigationIcon = {
                IconButton(onClick = { drawerOpen = true }) {
                    Icon(Icons.Default.Menu, contentDescription = "Menu")
                }
            }
        ) { padding ->
            when {
                state.isLoading -> LoadingIndicator()
                state.error != null -> EmptyState(message = state.error!!)
                state.summary != null -> DashboardContent(
                    summary = state.summary!!,
                    padding = padding,
                    onNavigateToSantri = onNavigateToSantri,
                    onNavigateToSickness = onNavigateToSickness,
                    onNavigateToMedicines = onNavigateToMedicines,
                    onNavigateToReferrals = onNavigateToReferrals
                )
            }
        }
    }
}

@Composable
private fun DrawerItem(icon: androidx.compose.ui.graphics.vector.ImageVector, label: String, onClick: () -> Unit) {
    NavigationDrawerItem(
        icon = { Icon(icon, contentDescription = null) },
        label = { Text(label) },
        selected = false,
        onClick = onClick,
        modifier = Modifier.padding(horizontal = 12.dp)
    )
}

@Composable
private fun DashboardContent(
    summary: DashboardSummary,
    padding: PaddingValues,
    onNavigateToSantri: () -> Unit,
    onNavigateToSickness: () -> Unit,
    onNavigateToMedicines: () -> Unit,
    onNavigateToReferrals: () -> Unit
) {
    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .padding(padding)
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        item {
            Text(
                text = "Selamat Datang",
                style = MaterialTheme.typography.titleLarge,
                fontWeight = FontWeight.Bold
            )
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                StatCard(
                    title = "Total Santri",
                    value = summary.totalSantri.toString(),
                    icon = Icons.Default.People,
                    color = Teal600,
                    modifier = Modifier.weight(1f)
                )
                StatCard(
                    title = "Kasus Aktif",
                    value = summary.activeCases.toString(),
                    icon = Icons.Default.MedicalServices,
                    color = Orange600,
                    modifier = Modifier.weight(1f)
                )
            }
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                StatCard(
                    title = "Kunjungan Hari Ini",
                    value = summary.todayVisits.toString(),
                    icon = Icons.Default.Today,
                    color = Blue600,
                    modifier = Modifier.weight(1f)
                )
                StatCard(
                    title = "Sembuh Hari Ini",
                    value = summary.recoveredToday.toString(),
                    icon = Icons.Default.CheckCircle,
                    color = Green600,
                    modifier = Modifier.weight(1f)
                )
            }
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                StatCard(
                    title = "Obat Hampir Habis",
                    value = summary.lowStockMedicines.toString(),
                    icon = Icons.Default.Warning,
                    color = Red600,
                    modifier = Modifier.weight(1f)
                )
                StatCard(
                    title = "Rujukan Pending",
                    value = summary.pendingReferrals.toString(),
                    icon = Icons.Default.LocalHospital,
                    color = Purple600,
                    modifier = Modifier.weight(1f)
                )
            }
        }

        item {
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = "Akses Cepat",
                style = MaterialTheme.typography.titleMedium,
                fontWeight = FontWeight.SemiBold
            )
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                QuickActionCard(
                    title = "Santri",
                    icon = Icons.Default.People,
                    color = Teal600,
                    onClick = onNavigateToSantri,
                    modifier = Modifier.weight(1f)
                )
                QuickActionCard(
                    title = "Santri Sakit",
                    icon = Icons.Default.MedicalServices,
                    color = Orange600,
                    onClick = onNavigateToSickness,
                    modifier = Modifier.weight(1f)
                )
            }
        }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                QuickActionCard(
                    title = "Obat",
                    icon = Icons.Default.Medication,
                    color = Blue600,
                    onClick = onNavigateToMedicines,
                    modifier = Modifier.weight(1f)
                )
                QuickActionCard(
                    title = "Rujukan RS",
                    icon = Icons.Default.LocalHospital,
                    color = Purple600,
                    onClick = onNavigateToReferrals,
                    modifier = Modifier.weight(1f)
                )
            }
        }

        if (summary.recentCases != null && summary.recentCases.isNotEmpty()) {
            item {
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = "Kasus Terbaru",
                    style = MaterialTheme.typography.titleMedium,
                    fontWeight = FontWeight.SemiBold
                )
            }

            items(summary.recentCases.take(5)) { case ->
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text(
                                text = case.santriName ?: "-",
                                style = MaterialTheme.typography.bodyMedium,
                                fontWeight = FontWeight.Medium
                            )
                            Text(
                                text = case.diagnosis ?: case.complaint ?: "-",
                                style = MaterialTheme.typography.bodySmall,
                                color = MaterialTheme.colorScheme.onSurfaceVariant
                            )
                        }
                        StatusBadge(status = case.status)
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(80.dp)) }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun QuickActionCard(
    title: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    color: androidx.compose.ui.graphics.Color,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        onClick = onClick,
        modifier = modifier.height(80.dp),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Icon(icon, contentDescription = null, tint = color, modifier = Modifier.size(28.dp))
            Spacer(modifier = Modifier.height(4.dp))
            Text(text = title, style = MaterialTheme.typography.labelLarge)
        }
    }
}
