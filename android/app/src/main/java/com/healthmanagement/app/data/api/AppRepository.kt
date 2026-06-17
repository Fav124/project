package com.healthmanagement.app.data.api

import com.healthmanagement.app.data.model.*
import okhttp3.MultipartBody
import okhttp3.RequestBody
import okhttp3.RequestBody.Companion.toRequestBody

class AppRepository {
    private val api = RetrofitClient.apiService

    private suspend fun <T> safeApiCall(call: suspend () -> retrofit2.Response<ApiResponse<T>>): Result<T> {
        return try {
            val response = call()
            if (response.isSuccessful) {
                val body = response.body()
                if (body != null && body.success && body.data != null) {
                    Result.success(body.data)
                } else {
                    Result.failure(Exception(body?.message ?: "Request failed"))
                }
            } else {
                val errorBody = response.errorBody()?.string()
                Result.failure(Exception(errorBody ?: "Error ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Auth
    suspend fun login(email: String, password: String) = safeApiCall {
        api.login(mapOf("email" to email, "password" to password))
    }

    suspend fun register(name: String, email: String, password: String, noHp: String) = safeApiCall {
        api.register(mapOf("name" to name, "email" to email, "password" to password, "no_hp" to noHp))
    }

    suspend fun logout() = safeApiCall { api.logout() }

    suspend fun getMe() = safeApiCall { api.getMe() }

    // Dashboard
    suspend fun getDashboardSummary() = safeApiCall { api.getDashboardSummary() }

    // Santri
    suspend fun getSantri(page: Int = 1, search: String? = null) = safeApiCall {
        api.getSantri(page, search)
    }

    suspend fun getSantriDetail(id: Int) = safeApiCall { api.getSantriDetail(id) }
    suspend fun getSantriLookups() = safeApiCall { api.getSantriLookups() }

    suspend fun createSantri(
        nis: String, name: String, gender: String, birthPlace: String, birthDate: String,
        classId: Int, majorId: Int, classRoom: String?,
        guardianName: String?, guardianPhone: String?, guardianRelationship: String?,
        bloodType: String?, height: String?, weight: String?, allergies: String?,
        medicalHistory: String?, notes: String?, photo: MultipartBody.Part?
    ) = safeApiCall {
        api.createSantri(
            nis.toRequestBody(), name.toRequestBody(), gender.toRequestBody(),
            birthPlace.toRequestBody(), birthDate.toRequestBody(),
            classId.toString().toRequestBody(), majorId.toString().toRequestBody(),
            classRoom?.toRequestBody(),
            guardianName?.toRequestBody(), guardianPhone?.toRequestBody(),
            guardianRelationship?.toRequestBody(), bloodType?.toRequestBody(),
            height?.toRequestBody(), weight?.toRequestBody(), allergies?.toRequestBody(),
            medicalHistory?.toRequestBody(), notes?.toRequestBody(), photo
        )
    }

    suspend fun updateSantri(
        id: Int, nis: String, name: String, gender: String, birthPlace: String, birthDate: String,
        classId: Int, majorId: Int, classRoom: String?,
        guardianName: String?, guardianPhone: String?, guardianRelationship: String?,
        bloodType: String?, height: String?, weight: String?, allergies: String?,
        medicalHistory: String?, notes: String?, photo: MultipartBody.Part?
    ) = safeApiCall {
        api.updateSantri(
            id, "PUT".toRequestBody(), nis.toRequestBody(), name.toRequestBody(),
            gender.toRequestBody(), birthPlace.toRequestBody(), birthDate.toRequestBody(),
            classId.toString().toRequestBody(), majorId.toString().toRequestBody(),
            classRoom?.toRequestBody(),
            guardianName?.toRequestBody(), guardianPhone?.toRequestBody(),
            guardianRelationship?.toRequestBody(), bloodType?.toRequestBody(),
            height?.toRequestBody(), weight?.toRequestBody(), allergies?.toRequestBody(),
            medicalHistory?.toRequestBody(), notes?.toRequestBody(), photo
        )
    }

    suspend fun deleteSantri(id: Int) = safeApiCall { api.deleteSantri(id) }

    // Guardians
    suspend fun getGuardians(santriId: Int) = safeApiCall { api.getGuardians(santriId) }
    suspend fun addGuardian(santriId: Int, name: String, relationship: String, phone: String) = safeApiCall {
        api.addGuardian(santriId, mapOf("name" to name, "relationship" to relationship, "phone" to phone))
    }

    // Master Data
    suspend fun getKelas() = safeApiCall { api.getKelas() }
    suspend fun createKelas(name: String, description: String?) = safeApiCall {
        api.createKelas(mapOf("name" to name, "description" to (description ?: "")))
    }
    suspend fun updateKelas(id: Int, name: String, description: String?) = safeApiCall {
        api.updateKelas(id, mapOf("name" to name, "description" to (description ?: "")))
    }
    suspend fun deleteKelas(id: Int) = safeApiCall { api.deleteKelas(id) }

    suspend fun getJurusan() = safeApiCall { api.getJurusan() }
    suspend fun createJurusan(name: String, description: String?) = safeApiCall {
        api.createJurusan(mapOf("name" to name, "description" to (description ?: "")))
    }
    suspend fun updateJurusan(id: Int, name: String, description: String?) = safeApiCall {
        api.updateJurusan(id, mapOf("name" to name, "description" to (description ?: "")))
    }
    suspend fun deleteJurusan(id: Int) = safeApiCall { api.deleteJurusan(id) }

    // Sickness Cases
    suspend fun getKunjunganFormData() = safeApiCall { api.getKunjunganFormData() }
    suspend fun getKunjungan(page: Int = 1, status: String? = null, search: String? = null) = safeApiCall {
        api.getKunjungan(page, status, search)
    }
    suspend fun getKunjunganDetail(id: Int) = safeApiCall { api.getKunjunganDetail(id) }
    suspend fun createKunjungan(data: Map<String, Any>) = safeApiCall { api.createKunjungan(data) }
    suspend fun updateKunjungan(id: Int, data: Map<String, Any>) = safeApiCall { api.updateKunjungan(id, data) }
    suspend fun deleteKunjungan(id: Int) = safeApiCall { api.deleteKunjungan(id) }
    suspend fun markRecovered(id: Int) = safeApiCall { api.markRecovered(id) }
    suspend fun notifyGuardianKunjungan(id: Int) = safeApiCall { api.notifyGuardianKunjungan(id) }

    // Medicines
    suspend fun getObat(page: Int = 1, search: String? = null, kategori: String? = null) = safeApiCall {
        api.getObat(page, search, kategori)
    }
    suspend fun getObatDetail(id: Int) = safeApiCall { api.getObatDetail(id) }
    suspend fun createObat(data: Map<String, String>) = safeApiCall { api.createObat(data) }
    suspend fun updateObat(id: Int, data: Map<String, String>) = safeApiCall { api.updateObat(id, data) }
    suspend fun deleteObat(id: Int) = safeApiCall { api.deleteObat(id) }
    suspend fun createMutasi(data: Map<String, Any>) = safeApiCall { api.createMutasi(data) }

    // Referrals
    suspend fun getRujukan(page: Int = 1, status: String? = null) = safeApiCall {
        api.getRujukan(page, status)
    }
    suspend fun getRujukanDetail(id: Int) = safeApiCall { api.getRujukanDetail(id) }
    suspend fun createRujukan(data: Map<String, Any>) = safeApiCall { api.createRujukan(data) }
    suspend fun updateRujukanStatus(id: Int, status: String) = safeApiCall {
        api.updateRujukanStatus(id, mapOf("status" to status))
    }
    suspend fun deleteRujukan(id: Int) = safeApiCall { api.deleteRujukan(id) }
    suspend fun notifyGuardianRujukan(id: Int) = safeApiCall { api.notifyGuardianRujukan(id) }

    // Reports
    suspend fun getDailySummary(startDate: String? = null, endDate: String? = null, period: String? = null) = safeApiCall {
        api.getDailySummary(startDate, endDate, period)
    }
    suspend fun getMedicineReport(startDate: String? = null, endDate: String? = null) = safeApiCall {
        api.getMedicineReport(startDate, endDate)
    }

    // Approvals
    suspend fun getApprovals() = safeApiCall { api.getApprovals() }
    suspend fun approveUser(userId: Int) = safeApiCall { api.approveUser(userId) }
    suspend fun rejectUser(userId: Int, reason: String) = safeApiCall {
        api.rejectUser(userId, mapOf("reason" to reason))
    }
}
