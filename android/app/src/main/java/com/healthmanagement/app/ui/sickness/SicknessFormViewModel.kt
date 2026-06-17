package com.healthmanagement.app.ui.sickness

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Medicine
import com.healthmanagement.app.data.model.Santri
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class MedicineEntry(
    val medicineId: Int? = null,
    val medicineName: String = "",
    val dosage: String = "",
    val quantity: String = "1",
    val frequency: String = ""
)

data class SicknessFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val isSaved: Boolean = false,
    val error: String? = null,
    val santriId: Int? = null,
    val santriName: String = "",
    val visitDate: String = "",
    val complaint: String = "",
    val diagnosis: String = "",
    val actionTaken: String = "",
    val medicineNotes: String = "",
    val notes: String = "",
    val medicines: List<MedicineEntry> = emptyList(),
    val santriList: List<Santri> = emptyList(),
    val medicineList: List<Medicine> = emptyList()
)

class SicknessFormViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SicknessFormState())
    val state: StateFlow<SicknessFormState> = _state.asStateFlow()

    fun load(id: Int?) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            repository.getKunjunganFormData().fold(
                onSuccess = { data ->
                    _state.value = _state.value.copy(santriList = data.santris, medicineList = data.medicines)
                },
                onFailure = {}
            )
            if (id != null) {
                repository.getKunjunganDetail(id).fold(
                    onSuccess = { case ->
                        _state.value = _state.value.copy(
                            isLoading = false,
                            santriId = case.santriId,
                            santriName = case.santri?.name ?: "",
                            visitDate = case.visitDate ?: "",
                            complaint = case.complaint ?: "",
                            diagnosis = case.diagnosis ?: "",
                            actionTaken = case.actionTaken ?: "",
                            medicineNotes = case.medicineNotes ?: "",
                            notes = case.notes ?: "",
                            medicines = case.medicines?.map {
                                MedicineEntry(it.medicineId, it.medicineName, it.dosage ?: "", it.quantity.toString(), it.frequency ?: "")
                            } ?: emptyList()
                        )
                    },
                    onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
                )
            } else {
                _state.value = _state.value.copy(isLoading = false)
            }
        }
    }

    fun onSantriChange(id: Int, name: String) { _state.value = _state.value.copy(santriId = id, santriName = name) }
    fun onVisitDateChange(v: String) { _state.value = _state.value.copy(visitDate = v) }
    fun onComplaintChange(v: String) { _state.value = _state.value.copy(complaint = v) }
    fun onDiagnosisChange(v: String) { _state.value = _state.value.copy(diagnosis = v) }
    fun onActionTakenChange(v: String) { _state.value = _state.value.copy(actionTaken = v) }
    fun onMedicineNotesChange(v: String) { _state.value = _state.value.copy(medicineNotes = v) }
    fun onNotesChange(v: String) { _state.value = _state.value.copy(notes = v) }

    fun addMedicine() {
        _state.value = _state.value.copy(medicines = _state.value.medicines + MedicineEntry())
    }

    fun removeMedicine(index: Int) {
        _state.value = _state.value.copy(medicines = _state.value.medicines.toMutableList().also { it.removeAt(index) })
    }

    fun onMedicineChange(index: Int, id: Int, name: String) {
        val list = _state.value.medicines.toMutableList()
        if (index < list.size) list[index] = list[index].copy(medicineId = id, medicineName = name)
        _state.value = _state.value.copy(medicines = list)
    }

    fun onMedicineDosageChange(index: Int, v: String) {
        val list = _state.value.medicines.toMutableList()
        if (index < list.size) list[index] = list[index].copy(dosage = v)
        _state.value = _state.value.copy(medicines = list)
    }

    fun onMedicineQuantityChange(index: Int, v: String) {
        val list = _state.value.medicines.toMutableList()
        if (index < list.size) list[index] = list[index].copy(quantity = v)
        _state.value = _state.value.copy(medicines = list)
    }

    fun onMedicineFrequencyChange(index: Int, v: String) {
        val list = _state.value.medicines.toMutableList()
        if (index < list.size) list[index] = list[index].copy(frequency = v)
        _state.value = _state.value.copy(medicines = list)
    }

    fun save(id: Int?) {
        val s = _state.value
        if (s.santriId == null) {
            _state.value = s.copy(error = "Santri harus dipilih")
            return
        }
        viewModelScope.launch {
            _state.value = s.copy(isSaving = true, error = null)

            val data = mutableMapOf<String, Any>(
                "santri_id" to s.santriId,
                "visit_date" to s.visitDate,
                "complaint" to s.complaint,
                "diagnosis" to s.diagnosis,
                "action_taken" to s.actionTaken,
                "medicine_notes" to s.medicineNotes,
                "notes" to s.notes,
                "status" to "handled"
            )

            if (s.medicines.isNotEmpty()) {
                data["medicines"] = s.medicines.map { med ->
                    mapOf<String, Any>(
                        "medicine_id" to (med.medicineId ?: 0),
                        "dosage" to med.dosage,
                        "quantity" to (med.quantity.toIntOrNull() ?: 1),
                        "frequency" to med.frequency
                    )
                }
            }

            val result = if (id != null) repository.updateKunjungan(id, data) else repository.createKunjungan(data)
            result.fold(
                onSuccess = { _state.value = _state.value.copy(isSaving = false, isSaved = true) },
                onFailure = { _state.value = _state.value.copy(isSaving = false, error = it.message) }
            )
        }
    }
}
