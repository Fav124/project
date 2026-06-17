package com.healthmanagement.app.ui.santri

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.SantriDetail
import com.healthmanagement.app.data.model.SicknessCase
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SantriDetailState(
    val santri: SantriDetail? = null,
    val sicknessCases: List<SicknessCase>? = null,
    val isLoading: Boolean = false,
    val error: String? = null
)

class SantriDetailViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SantriDetailState())
    val state: StateFlow<SantriDetailState> = _state.asStateFlow()

    fun loadSantri(id: Int) {
        viewModelScope.launch {
            _state.value = SantriDetailState(isLoading = true)
            repository.getSantriDetail(id).fold(
                onSuccess = {
                    _state.value = SantriDetailState(
                        santri = it,
                        sicknessCases = it.sicknessCases
                    )
                },
                onFailure = { _state.value = SantriDetailState(error = it.message) }
            )
        }
    }
}
