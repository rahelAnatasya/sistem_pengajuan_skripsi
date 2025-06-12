Berikut adalah diagram Mermaid yang lebih singkat dan fokus pada alur utama:

## 1. Alur Login

```mermaid
flowchart TD
    A[Login Page] --> B{Input Valid?}
    B -->|No| A
    B -->|Yes| C[Check Database]
    C --> D{User Found?}
    D -->|No| A
    D -->|Yes| E[Create Session]
    E --> F[Redirect Dashboard]
```

## 2. Alur Register

```mermaid
flowchart TD
    A[Register Page] --> B[Choose Role]
    B --> C[Fill Form]
    C --> D[Submit Data]
    D --> E{Valid Data?}
    E -->|No| C
    E -->|Yes| F[Insert Database]
    F --> G[Redirect Login]
```

## 3. Alur Mahasiswa Mengajukan Skripsi

```mermaid
flowchart TD
    A[Mahasiswa Login] --> B[Pengajuan Form]
    B --> C[Fill: Judul, Deskripsi, Bidang, Dosen]
    C --> D[Submit]
    D --> E[Insert Database]
    E --> F[Status: Pending]
    F --> G[Success Message]
```

## 4. Alur Dosen Review Skripsi

```mermaid
flowchart TD
    A[Dosen Login] --> B[Review Page]
    B --> C[View Pending Submissions]
    C --> D{Choose Action?}
    D -->|Approve| E[Status: Approved]
    D -->|Reject| F[Status: Rejected]
    E --> G[Add Comments]
    F --> G
    G --> H[Update Database]
    H --> I[Notify Student]
```

## 5. Alur Search and Filter

```mermaid
flowchart TD
    A[Public Page] --> B[Select Filter]
    B --> C[Enter Search Term]
    C --> D[Execute Query]
    D --> E[Display Results]
    E --> F{More Filters?}
    F -->|Yes| B
    F -->|No| G[View Details]
```

## Alur Keseluruhan Sistem

```mermaid
flowchart TD
    A[Start] --> B[Login/Register]
    B --> C{Role?}
    C -->|Mahasiswa| D[Submit Proposal]
    C -->|Dosen| E[Review Proposals]
    C -->|Public| F[Search & View]
    D --> G[Wait Review]
    E --> H[Approve/Reject]
    F --> I[Filter Results]
    G --> E
    H --> J[Update Status]
    I --> K[View Details]
    J --> L[End]
    K --> L
```