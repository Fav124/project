package com.healthmanagement.app.ui.settings

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import com.healthmanagement.app.data.api.SessionManager
import com.healthmanagement.app.ui.components.AppScaffold
import com.healthmanagement.app.ui.components.SectionHeader
import com.google.gson.Gson
import com.google.gson.JsonParser
import com.google.gson.JsonObject
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SettingsScreen(
    onLogout: () -> Unit,
    onNavigateToUsers: () -> Unit,
    onBack: () -> Unit
) {
    val scope = rememberCoroutineScope()
    var loggedOut by remember { mutableStateOf(false) }

    val userJson = SessionManager.userData.collectAsState().value
    val userRole = SessionManager.userRole.collectAsState().value

    LaunchedEffect(loggedOut) { if (loggedOut) onLogout() }

    val userName = userJson?.let {
        try { JsonParser().parse(it).asJsonObject.get("name")?.asString ?: "User" } catch (e: Exception) { "User" }
    } ?: "User"
    val userEmail = userJson?.let {
        try { JsonParser().parse(it).asJsonObject.get("email")?.asString ?: "" } catch (e: Exception) { "" }
    } ?: ""

    AppScaffold(title = "Pengaturan", onBack = onBack) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(16.dp)
            ) {
                Column(
                    modifier = Modifier.fillMaxWidth().padding(20.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Icon(Icons.Default.AccountCircle, contentDescription = null, modifier = Modifier.size(64.dp), tint = MaterialTheme.colorScheme.primary)
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(text = userName, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                    Text(text = userEmail, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    Spacer(modifier = Modifier.height(4.dp))
                    val roleLabel = when (userRole) {
                        "super_admin" -> "Super Admin"
                        "admin" -> "Admin"
                        "petugas_kesehatan" -> "Petugas Kesehatan"
                        else -> userRole ?: "-"
                    }
                    AssistChip(onClick = {}, label = { Text(roleLabel) }, leadingIcon = { Icon(Icons.Default.Badge, contentDescription = null, modifier = Modifier.size(16.dp)) })
                }
            }

            SectionHeader(title = "Menu")

            SettingsItem(icon = Icons.Default.People, title = "Manajemen Pengguna", subtitle = "Kelola akun pengguna", onClick = onNavigateToUsers, visible = userRole == "super_admin")

            HorizontalDivider()

            Card(
                onClick = {
                    scope.launch {
                        SessionManager.logout()
                        loggedOut = true
                    }
                },
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.errorContainer)
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth().padding(16.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(Icons.Default.Logout, contentDescription = null, tint = MaterialTheme.colorScheme.error)
                    Spacer(modifier = Modifier.width(12.dp))
                    Text(text = "Keluar", color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                }
            }
        }
    }
}

@Composable
private fun SettingsItem(
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    title: String,
    subtitle: String? = null,
    onClick: () -> Unit,
    visible: Boolean = true
) {
    if (!visible) return
    Card(
        onClick = onClick,
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(icon, contentDescription = null, tint = MaterialTheme.colorScheme.primary)
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = title, style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                if (subtitle != null) {
                    Text(text = subtitle, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                }
            }
            Icon(Icons.Default.ChevronRight, contentDescription = null, tint = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}
