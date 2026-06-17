package com.healthmanagement.app.ui.sickness

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.SicknessCaseDetail
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SicknessDetailState(
    val case: SicknessCaseDetail? = null,
    val isLoading: Boolean = false,
    val error: String? = null
)

class SicknessDetailViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SicknessDetailState())
    val state: StateFlow<SicknessDetailState> = _state.asStateFlow()

    fun load(id: Int) {
        viewModelScope.launch {
            _state.value = SicknessDetailState(isLoading = true)
            repository.getKunjunganDetail(id).fold(
                onSuccess = { _state.value = SicknessDetailState(case = it) },
                onFailure = { _state.value = SicknessDetailState(error = it.message) }
            )
        }
    }
}
