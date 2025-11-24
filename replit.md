# Sistema de Emissão de APAC

## Overview
This project is a web-based system developed in pure PHP for managing and issuing Autorizações de Procedimentos de Alta Complexidade (APAC) for the Brazilian Unified Health System (SUS). Its primary purpose is to streamline the process of issuing APACs, managing number ranges (faixas), and providing audit trails. The system aims to offer a robust, secure, and user-friendly platform for healthcare administrators.

## User Preferences
I prefer detailed explanations.
I want iterative development.
Ask before making major changes.

## System Architecture
The system uses a simplified MVC architecture implemented in pure PHP 8.3.
-   **Frontend**: HTML5, Tailwind CSS (via CDN), and Alpine.js (via CDN) provide a modern and reactive user interface.
-   **Backend**: PHP 8.3 handles server-side logic without frameworks, using PDO for database interaction.
-   **Database**: MySQL 5.7 is used as the primary data store, with a robust schema comprising 12 tables for managing users, APACs, number ranges, patients, medical reports, and auxiliary data.
-   **Security**: Features include password hashing (bcrypt), prepared statements for SQL injection prevention, `htmlspecialchars()` for XSS prevention, and session management with ID regeneration for CSRF protection.
-   **Core Features**:
    -   **Authentication**: Secure user login/logout and route protection via middleware.
    -   **Dashboard**: Real-time statistics on APAC ranges, issued APACs, and recent activity.
    -   **APAC Range Management (Faixas)**: Full CRUD operations for managing 13-digit APAC number ranges, including status tracking (Available, In Use, Exhausted) and automatic quantity calculation.
    -   **APAC Issuance**: Individual APAC issuance, automatic 14-digit number generation (13 digits + DV), cyclic algorithm for Dígito Verificador (DV) calculation, duplicate prevention, and sequential control within ranges.
    -   **Logging & Audit**: Automatic logging of all significant actions (e.g., range creation, APAC issuance, print status updates) with user and timestamp details.
-   **Design Patterns**: 
    -   **BaseModel**: An abstract class that centralizes generic CRUD logic for all specialized models, promoting DRY principles, maintainability, and scalability.
    -   **BaseController**: Abstract controller class with common methods (`render()`, `redirect()`, `jsonResponse()`, `flash()`) and helper utilities (`getFlash()`, `isAjax()`, `getMethod()`, `getInput()`). Accepts optional Database instance in constructor for dependency injection while defaulting to singleton pattern.
    -   **AuthMiddleware**: Centralized authentication middleware with session management, providing methods like `handle()` for route protection, `getLoggedInUser()` for user data retrieval, `isAuthenticated()` for auth checks, and `checkPermission()` for role-based access control. Automatically differentiates between web (redirect) and API (JSON 401) requests.
-   **Routing System**: Custom Router class (`src/Utils/Router.php`) handles 77 RESTful routes across 7 resource controllers (Pacientes, CIDs, Procedimentos, Estabelecimentos, Profissionais, Caráter de Atendimento, Laudos). Each controller has 9 standard routes: index (GET /resource), create (GET /resource/create), store (POST /resource), show (GET /resource/{id}), edit (GET /resource/{id}/edit), update (POST /resource/{id}/update), delete (POST /resource/{id}/delete), ajax_search (GET /resource/ajax/search), and ajax_list (GET /resource/ajax/list). All routes are protected by AuthMiddleware integration within controllers.
-   **Business Logic Services**: Three specialized services handle core business logic:
    -   **ValidacaoService**: Static validation methods for Brazilian documents with full compliance to official algorithms:
        -   **CNS (Cartão Nacional de Saúde)**: 15 digits with two validation modes: Definitivos (start with 1/2) use PIS-based algorithm with special DV=10 rule (adds 2 to sum and recalculates, resulting in "001"+DV format), DV=11 converts to 0, normal cases use "000"+DV format; Provisórios (start with 7/8/9) use ponderada sum where (sum % 11) must equal 0
        -   **CPF**: 11 digits with 2 verification digits using modulo-11 algorithm
        -   **CNPJ**: 14 digits with 2 verification digits using modulo-11 algorithm with cycling weights
        -   **CEP, Email, Dates**: Format and business validation
        -   **Sanitization methods**: limparCpf, limparCns, limparCnpj, limparCep, formatarData for data normalization
        -   **API endpoints**: `/api/validar-cns`, `/api/validar-cpf`, `/api/validar-cnpj`, `/api/validar-cep`, `/api/validar-email`
    -   **ApacService**: Manages APAC number generation using cyclic sequence "78900123456" for check digit calculation via modulo 11. Methods include gerarNumeroApac (13→14 digits), calcularDigitoVerificador, validarNumeroApac, obterProximoNumeroDisponivel (with automatic faixa status management), and verificarDuplicidade.
    -   **LaudoService**: Orchestrates complete laudo emission within PDO transactions. Supports both existing patients (via paciente_id) and new patient creation (via paciente array). Flow: validate data → beginTransaction → create/fetch patient (with CNS/CPF/date validation via ValidacaoService and data sanitization) → create laudo → optionally generate APAC → link APAC to laudo → commit. All operations wrapped in try/catch with automatic rollback on errors.
-   **UI/UX Decisions**: The system leverages Tailwind CSS for utility-first styling and Alpine.js for lightweight interactivity, aiming for a clean and functional interface.
-   **View System**: Modern, responsive views with hierarchical navigation:
    -   **Navigation**: Collapsible sidebar with 5 main sections (Dashboard, Laudos e APACs, Cadastros, Gerencial, Configurações), responsive mobile menu with drawer, contextual breadcrumbs with dynamic ID resolution
    -   **Laudos Views**: Multi-tab form for creation (4 steps: Paciente → Laudo → Autorização → Resumo), listing with AJAX search and pagination, detailed view with action sidebar
    -   **Pacientes Views**: Complete CRUD with 4 views (index with real-time AJAX search and pagination, create with CNS/CPF validation, edit with ViaCEP integration, show with laudo history card showing last 5 records), all forms include Brazilian document validation (CNS/CPF) and automatic address completion via ViaCEP API. The AJAX search implementation includes:
        -   **Real-time Search**: Debounced search input (500ms) that searches across CNS, CPF, and patient name fields
        -   **Pagination**: Client-side pagination with 10 records per page, showing page numbers and navigation controls
        -   **Loading States**: Visual feedback during search operations with spinner indicator
        -   **Empty States**: User-friendly messages when no patients are found
        -   **API Endpoint**: `/pacientes/ajax/search` accepting `q` (search term) and `page` parameters, returning JSON with `pacientes`, `total`, `totalPages`, and `currentPage`
    -   **Estabelecimentos Views**: Complete CRUD with 3 views (index with real-time AJAX search and pagination, form for create/edit with CNPJ validation and ViaCEP integration, show with laudo history showing last 10 records), all forms include CNES (7 digits) and CNPJ (14 digits) validation, automatic address completion via ViaCEP API, and status management (ativo/inativo). The AJAX search implementation includes:
        -   **Real-time Search**: Debounced search input (500ms) that searches across CNES, CNPJ, and razão social fields
        -   **Pagination**: Server-side pagination with SQL LIMIT/OFFSET (10 records per page)
        -   **CNPJ Formatting**: Automatic CNPJ formatting (XX.XXX.XXX/XXXX-XX) in table display
        -   **Model Methods**: findPaginated, searchPaginated, searchCount, countTotal, getLaudos, findByCnes, findByCnpj
        -   **API Endpoint**: `/estabelecimentos/ajax/search` accepting `q` (search term) and `page` parameters, returning JSON with `estabelecimentos`, `total`, `totalPages`, and `currentPage`
    -   **Relatórios Views**: Statistics dashboard with filters, export buttons (PDF/Excel), chart placeholders
    -   **Logs Views**: Activity audit table with filtering, pagination, timestamped user actions

## External Dependencies
-   **Database**: MySQL 5.7 (hosted remotely)
-   **Frontend Libraries (via CDN)**:
    -   Tailwind CSS
    -   Alpine.js