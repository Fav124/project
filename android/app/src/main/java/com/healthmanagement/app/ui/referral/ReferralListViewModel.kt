package com.healthmanagement.app.ui.referral

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.HospitalReferral
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ReferralListState(
    val referrals: List<HospitalReferral> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null,
    val statusFilter: String? = null
)

class ReferralListViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(ReferralListState())
    val state: StateFlow<ReferralListState> = _state.asStateFlow()

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            repository.getRujukan(status = _state.value.statusFilter).fold(
                onSuccess = { _state.value = _state.value.copy(referrals = it.data, isLoading = false) },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    fun onFilterChange(status: String?) {
        _state.value = _state.value.copy(statusFilter = status)
        load()
    }

    fun updateStatus(id: Int, status: String) {
        viewModelScope.launch {
            repository.updateRujukanStatus(id, status).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun delete(id: Int) {
        viewModelScope.launch {
            repository.deleteRujukan(id).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }
}
