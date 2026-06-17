package com.healthmanagement.app.ui.medicines

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.MedicineDetail
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class MedicineFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val isSaved: Boolean = false,
    val error: String? = null,
    val isEdit: Boolean = false,
    val kodeObat: String = "",
    val name: String = "",
    val kategori: String = "",
    val bentukSediaan: String = "",
    val stock: String = "0",
    val minimumStock: String = "0",
    val unit: String = "",
    val expiryDate: String = "",
    val lokasiPenyimpanan: String = "",
    val description: String = ""
)

class MedicineFormViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(MedicineFormState())
    val state: StateFlow<MedicineFormState> = _state.asStateFlow()

    fun load(id: Int?) {
        if (id == null) return
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, isEdit = true)
            repository.getObatDetail(id).fold(
                onSuccess = { med ->
                    _state.value = _state.value.copy(
                        isLoading = false,
                        kodeObat = med.kodeObat ?: "",
                        name = med.name,
                        kategori = med.kategori ?: "",
                        bentukSediaan = med.bentukSediaan ?: "",
                        stock = med.stock.toString(),
                        minimumStock = med.minimumStock.toString(),
                        unit = med.unit ?: "",
                        expiryDate = med.expiryDate ?: "",
                        lokasiPenyimpanan = med.lokasiPenyimpanan ?: "",
                        description = med.description ?: ""
                    )
                },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    fun onKodeObatChange(v: String) { _state.value = _state.value.copy(kodeObat = v) }
    fun onNameChange(v: String) { _state.value = _state.value.copy(name = v) }
    fun onKategoriChange(v: String) { _state.value = _state.value.copy(kategori = v) }
    fun onBentukSediaanChange(v: String) { _state.value = _state.value.copy(bentukSediaan = v) }
    fun onStockChange(v: String) { _state.value = _state.value.copy(stock = v) }
    fun onMinimumStockChange(v: String) { _state.value = _state.value.copy(minimumStock = v) }
    fun onUnitChange(v: String) { _state.value = _state.value.copy(unit = v) }
    fun onExpiryDateChange(v: String) { _state.value = _state.value.copy(expiryDate = v) }
    fun onLokasiPenyimpananChange(v: String) { _state.value = _state.value.copy(lokasiPenyimpanan = v) }
    fun onDescriptionChange(v: String) { _state.value = _state.value.copy(description = v) }

    fun save(id: Int?) {
        val s = _state.value
        if (s.name.isBlank()) { _state.value = s.copy(error = "Nama obat harus diisi"); return }
        viewModelScope.launch {
            _state.value = s.copy(isSaving = true, error = null)
            val data = mapOf(
                "kode_obat" to s.kodeObat, "name" to s.name, "kategori" to s.kategori,
                "bentuk_sediaan" to s.bentukSediaan, "stock" to s.stock, "minimum_stock" to s.minimumStock,
                "unit" to s.unit, "expiry_date" to s.expiryDate, "lokasi_penyimpanan" to s.lokasiPenyimpanan,
                "description" to s.description
            )
            val result = if (id != null) repository.updateObat(id, data) else repository.createObat(data)
            result.fold(
                onSuccess = { _state.value = _state.value.copy(isSaving = false, isSaved = true) },
                onFailure = { _state.value = _state.value.copy(isSaving = false, error = it.message) }
            )
        }
    }
}
