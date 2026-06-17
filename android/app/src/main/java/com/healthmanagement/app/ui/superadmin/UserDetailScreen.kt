package com.healthmanagement.app.ui.superadmin

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.UserApproval
import com.healthmanagement.app.ui.components.AppScaffold
import com.healthmanagement.app.ui.components.LoadingIndicator
import com.healthmanagement.app.ui.components.StatusBadge

@Composable
fun UserDetailScreen(
    userId: Int,
    onBack: () -> Unit
) {
    val repository = remember { AppRepository() }
    var user by remember { mutableStateOf<UserApproval?>(null) }
    var isLoading by remember { mutableStateOf(true) }

    LaunchedEffect(userId) {
        repository.getApprovals().fold(
            onSuccess = { users -> user = users.find { it.id == userId } },
            onFailure = {}
        )
        isLoading = false
    }

    AppScaffold(title = "Detail Pengguna", onBack = onBack) { padding ->
        if (isLoading) {
            LoadingIndicator()
        } else if (user == null) {
            Box(modifier = Modifier.fillMaxSize().padding(padding), contentAlignment = Alignment.Center) {
                Text("Pengguna tidak ditemukan")
            }
        } else {
            val u = user!!
            Column(
                modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp)) {
                    Column(modifier = Modifier.fillMaxWidth().padding(20.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Default.AccountCircle, contentDescription = null, modifier = Modifier.size(72.dp), tint = MaterialTheme.colorScheme.primary)
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(text = u.name, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                        Text(text = u.email, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        Spacer(modifier = Modifier.height(4.dp))
                        StatusBadge(status = u.status)
                    }
                }

                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text(text = "Informasi", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                        Spacer(modifier = Modifier.height(8.dp))
                        DetailRow("Nama", u.name)
                        DetailRow("Email", u.email)
                        DetailRow("No. HP", u.noHp ?: "-")
                        DetailRow("Role", u.role)
                        DetailRow("Status", u.status)
                        DetailRow("Jabatan", u.jobTitle ?: "-")
                        DetailRow("Tgl Daftar", u.createdAt?.take(10) ?: "-")
                    }
                }
            }
        }
    }
}

@Composable
private fun DetailRow(label: String, value: String) {
    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
        Text(text = label, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, modifier = Modifier.width(100.dp))
        Text(text = value, style = MaterialTheme.typography.bodyMedium)
    }
}
