alur 
1. login
2. register
3. mahasiswa mengajukan skripsi
4. alur dosen review skripsi
5. search and filter


## 1. Alur Login

```pseudocode
ALGORITHM LoginProcess
BEGIN
    IF user_already_logged_in THEN
        REDIRECT to dashboard.php
        EXIT
    END IF
    
    IF POST_request_received THEN
        GET username FROM form_input
        GET password FROM form_input
        
        CONNECT to database
        QUERY user_data WHERE username = input_username
        
        IF user_found THEN
            IF password_verify(input_password, stored_password) THEN
                CREATE session_variables:
                    - user_id = user.id
                    - username = user.username  
                    - role = user.role
                    - nama_lengkap = user.nama_lengkap
                REDIRECT to dashboard.php
            ELSE
                SET error = "Password salah!"
            END IF
        ELSE
            SET error = "Username tidak ditemukan!"
        END IF
    END IF
    
    DISPLAY login_form WITH error_message
END
```

## 2. Alur Register

```pseudocode
ALGORITHM RegisterProcess
BEGIN
    IF user_already_logged_in THEN
        REDIRECT to dashboard.php
        EXIT
    END IF
    
    IF POST_request_received THEN
        GET username, email, password, role, nama_lengkap FROM form_input
        
        IF role = "mahasiswa" THEN
            GET nim FROM form_input
            SET nidn = NULL
        ELSE IF role = "dosen" THEN
            GET nidn FROM form_input
            SET nim = NULL
        END IF
        
        HASH password USING password_hash()
        
        TRY
            CONNECT to database
            INSERT INTO users (username, email, password, role, nama_lengkap, nim, nidn)
            VALUES (input_values)
            
            IF insert_successful THEN
                SET success = "Registrasi berhasil! Silakan login."
            END IF
        CATCH database_error
            SET error = database_error_message
        END TRY
    END IF
    
    DISPLAY register_form WITH dynamic_fields_based_on_role
    DISPLAY success_or_error_message
END
```

## 3. Alur Mahasiswa Mengajukan Skripsi

```pseudocode
ALGORITHM MahasiswaPengajuanProcess
BEGIN
    CHECK user_login_status()
    CHECK user_role = "mahasiswa" OR redirect_to_unauthorized()
    
    CONNECT to database
    QUERY dosen_list WHERE role = "dosen"
    
    IF POST_request_received THEN
        GET judul_skripsi FROM form_input
        GET deskripsi FROM form_input
        GET bidang_studi FROM form_input
        GET dosen_pembimbing_id FROM form_input
        
        TRY
            INSERT INTO pengajuan_judul (
                mahasiswa_id = session.user_id,
                judul_skripsi = input_judul,
                deskripsi = input_deskripsi,
                bidang_studi = input_bidang,
                dosen_pembimbing_id = input_dosen_id,
                status = "pending"
            )
            
            IF insert_successful THEN
                SET success = "Pengajuan judul berhasil dikirim!"
            END IF
        CATCH database_error
            SET error = database_error_message
        END TRY
    END IF
    
    DISPLAY pengajuan_form WITH:
        - dosen_dropdown_list
        - bidang_studi_options
        - success_or_error_message
END
```

## 4. Alur Dosen Review Skripsi

```pseudocode
ALGORITHM DosenReviewProcess
BEGIN
    CHECK user_login_status()
    CHECK user_role = "dosen" OR redirect_to_unauthorized()
    
    CONNECT to database
    
    IF POST_request_received AND action_specified THEN
        GET pengajuan_id FROM form_input
        GET action FROM form_input  // "approve" or "reject"
        GET komentar FROM form_input
        
        IF action = "approve" THEN
            SET status = "approved"
        ELSE
            SET status = "rejected"
        END IF
        
        TRY
            UPDATE pengajuan_judul 
            SET status = new_status, catatan = input_komentar
            WHERE id = pengajuan_id AND dosen_pembimbing_id = session.user_id
            
            IF update_successful THEN
                SET success = "Status pengajuan berhasil diperbarui!"
            END IF
        CATCH database_error
            SET error = database_error_message
        END TRY
    END IF
    
    QUERY pengajuan_list WHERE dosen_pembimbing_id = session.user_id
    JOIN WITH mahasiswa_data
    ORDER BY created_at DESC
    
    CALCULATE statistics:
        - pending_count
        - approved_count  
        - rejected_count
    
    FOR EACH pengajuan IN pengajuan_list DO
        DISPLAY pengajuan_card WITH:
            - mahasiswa_info (nama, nim)
            - judul_skripsi
            - deskripsi
            - bidang_studi
            - status_badge
            - IF status = "pending" THEN
                DISPLAY review_form WITH approve/reject_buttons
            END IF
            - IF catatan_exists THEN
                DISPLAY existing_catatan
            END IF
    END FOR
    
    DISPLAY statistics_summary
END
```

## 5. Alur Search and Filter

```pseudocode
ALGORITHM SearchAndFilterProcess
BEGIN
    CHECK user_login_status()
    
    CONNECT to database
    
    // Get filter parameters
    GET status_filter FROM GET_request OR set_default("all")
    GET search_term FROM GET_request OR set_default("")
    
    // Build dynamic query
    INITIALIZE where_conditions = []
    INITIALIZE query_parameters = []
    
    IF status_filter ≠ "all" THEN
        ADD "p.status = :status" TO where_conditions
        ADD status_filter TO query_parameters
    END IF
    
    IF search_term ≠ empty THEN
        ADD "(p.judul_skripsi LIKE :search OR u1.nama_lengkap LIKE :search OR p.bidang_studi LIKE :search)" TO where_conditions
        ADD "%search_term%" TO query_parameters
    END IF
    
    // Construct final query
    IF where_conditions NOT empty THEN
        SET where_clause = "WHERE " + JOIN(where_conditions, " AND ")
    ELSE
        SET where_clause = ""
    END IF
    
    QUERY pengajuan_with_filters = "
        SELECT p.*, u1.nama_lengkap as nama_mahasiswa, u1.nim, u2.nama_lengkap as nama_dosen
        FROM pengajuan_judul p 
        JOIN users u1 ON p.mahasiswa_id = u1.id 
        LEFT JOIN users u2 ON p.dosen_pembimbing_id = u2.id 
        " + where_clause + "
        ORDER BY p.created_at DESC"
    
    EXECUTE query WITH query_parameters
    GET filtered_results
    
    // Get overall statistics
    QUERY statistics = "
        SELECT COUNT(*) as total,
               SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
               SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
               SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM pengajuan_judul"
    
    DISPLAY search_filter_form WITH:
        - status_dropdown (all, pending, approved, rejected)
        - search_input_field
        - current_filter_values
    
    DISPLAY statistics_cards WITH total_counts
    
    IF filtered_results IS empty THEN
        DISPLAY "Tidak ada pengajuan yang ditemukan"
    ELSE
        FOR EACH pengajuan IN filtered_results DO
            DISPLAY pengajuan_card WITH:
                - mahasiswa_info
                - judul_skripsi  
                - bidang_studi
                - status_badge
                - dosen_pembimbing (if assigned)
                - collapsible_deskripsi
                - collapsible_catatan (if exists)
        END FOR
    END IF
    
    DISPLAY navigation_buttons:
        - back_to_dashboard
        - IF user_role = "mahasiswa" THEN
            DISPLAY "Ajukan Judul Baru" button
        END IF
END
```

## Alur Umum Session Management

```pseudocode
ALGORITHM SessionManagement
BEGIN
    FUNCTION checkLogin()
        IF session.user_id NOT exists THEN
            REDIRECT to login.php
            EXIT
        END IF
    END FUNCTION
    
    FUNCTION checkRole(required_role)
        CALL checkLogin()
        IF session.role ≠ required_role THEN
            REDIRECT to unauthorized.php
            EXIT
        END IF
    END FUNCTION
    
    FUNCTION isLoggedIn()
        RETURN session.user_id EXISTS
    END FUNCTION
    
    FUNCTION logout()
        DESTROY all_session_data
        REDIRECT to login.php
    END FUNCTION
END
```

Pseudocode ini mencerminkan struktur dan logika yang ada dalam kodebase Anda, termasuk validasi, query database, session management, dan flow control yang diimplementasikan dalam file-file PHP.