package com.healthmanagement.app.ui.dashboard

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.api.SessionManager
import com.healthmanagement.app.data.model.DashboardSummary
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class DashboardState(
    val summary: DashboardSummary? = null,
    val isLoading: Boolean = false,
    val error: String? = null,
    val loggedOut: Boolean = false
)

class DashboardViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(DashboardState())
    val state: StateFlow<DashboardState> = _state.asStateFlow()

    fun loadDashboard() {
        viewModelScope.launch {
            _state.value = DashboardState(isLoading = true)
            repository.getDashboardSummary().fold(
                onSuccess = { _state.value = DashboardState(summary = it) },
                onFailure = { _state.value = DashboardState(error = it.message) }
            )
        }
    }

    fun logout() {
        viewModelScope.launch {
            SessionManager.logout()
            _state.value = _state.value.copy(loggedOut = true)
        }
    }
}
