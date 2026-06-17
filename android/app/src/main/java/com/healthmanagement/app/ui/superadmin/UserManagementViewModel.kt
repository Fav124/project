package com.healthmanagement.app.ui.superadmin

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.UserApproval
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class UserManagementState(
    val users: List<UserApproval> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null
)

class UserManagementViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(UserManagementState())
    val state: StateFlow<UserManagementState> = _state.asStateFlow()

    fun load() {
        viewModelScope.launch {
            _state.value = UserManagementState(isLoading = true)
            repository.getApprovals().fold(
                onSuccess = { _state.value = UserManagementState(users = it) },
                onFailure = { _state.value = UserManagementState(error = it.message) }
            )
        }
    }

    fun approve(userId: Int) {
        viewModelScope.launch {
            repository.approveUser(userId).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun reject(userId: Int) {
        viewModelScope.launch {
            repository.rejectUser(userId, "Ditolak oleh admin").fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }
}
