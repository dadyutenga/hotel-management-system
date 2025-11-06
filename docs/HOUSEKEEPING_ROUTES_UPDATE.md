# Housekeeping Module Routes Update

## Date: November 6, 2025

## Overview
The housekeeping routes have been restructured to follow the project's standard patterns and clearly separate Supervisor and Housekeeper functionalities.

---

## Route Structure

### 1. **Supervisor Routes** (Prefix: `/housekeeping`)
**Route Name Prefix:** `tenant.housekeeping.`

These routes are for supervisors who can create, view, edit, delete, and manage all housekeeping tasks.

| Method | URI | Controller Method | Route Name | Description |
|--------|-----|-------------------|------------|-------------|
| GET | `/housekeeping` | `index` | `tenant.housekeeping.index` | View all housekeeping tasks |
| GET | `/housekeeping/create` | `create` | `tenant.housekeeping.create` | Show form to create new task |
| POST | `/housekeeping` | `store` | `tenant.housekeeping.store` | Store new task |
| GET | `/housekeeping/{housekeeping}` | `show` | `tenant.housekeeping.show` | View specific task details |
| GET | `/housekeeping/{housekeeping}/edit` | `edit` | `tenant.housekeeping.edit` | Show form to edit task |
| PUT | `/housekeeping/{housekeeping}` | `update` | `tenant.housekeeping.update` | Update task details |
| DELETE | `/housekeeping/{housekeeping}` | `destroy` | `tenant.housekeeping.destroy` | Delete task |
| PUT | `/housekeeping/{housekeeping}/status` | `updateStatus` | `tenant.housekeeping.update-status` | Update task status |
| PUT | `/housekeeping/{housekeeping}/assign` | `assign` | `tenant.housekeeping.assign` | Assign task to housekeeper(s) |
| PUT | `/housekeeping/{housekeeping}/complete` | `markComplete` | `tenant.housekeeping.mark-complete` | Mark task as completed |
| POST | `/housekeeping/create-for-dirty-rooms` | `createForDirtyRooms` | `tenant.housekeeping.create-for-dirty-rooms` | Bulk create tasks for dirty rooms |

---

### 2. **Housekeeper Routes** (Prefix: `/housekeeper`)
**Route Name Prefix:** `tenant.housekeeper.`

These routes are for housekeepers who can only view and update their assigned tasks.

| Method | URI | Controller Method | Route Name | Description |
|--------|-----|-------------------|------------|-------------|
| GET | `/housekeeper/tasks` | `myTasks` | `tenant.housekeeper.tasks.index` | View my assigned tasks |
| GET | `/housekeeper/tasks/today` | `todayTasks` | `tenant.housekeeper.tasks.today` | View today's assigned tasks |
| GET | `/housekeeper/tasks/{task}` | `showTask` | `tenant.housekeeper.tasks.show` | View specific task details |
| POST | `/housekeeper/tasks/{task}/start` | `startTask` | `tenant.housekeeper.tasks.start` | Start working on task (Pending → In Progress) |
| POST | `/housekeeper/tasks/{task}/complete` | `completeTask` | `tenant.housekeeper.tasks.complete` | Complete task (In Progress → Completed) |
| POST | `/housekeeper/tasks/{task}/progress` | `updateTaskProgress` | `tenant.housekeeper.tasks.progress` | Update task progress |
| GET | `/housekeeper/statistics` | `myStatistics` | `tenant.housekeeper.statistics` | View my performance statistics |

---

## Role-Based Access Control

### **Supervisor Role** (`SUPERVISOR`)
✅ **Can:**
- Create housekeeping tasks with title, room number, notes, priority, deadline
- Assign tasks to one or multiple housekeepers
- View all tasks in a clean table
- Filter tasks by: Pending, In Progress, Completed
- Mark any task as Completed
- Edit task details
- Delete tasks

❌ **Cannot:**
- N/A (Full access to task management)

---

### **Housekeeper Role** (`HOUSEKEEPER`)
✅ **Can:**
- View ONLY tasks assigned to them
- View today's tasks
- Update task status: Pending → In Progress → Completed
- View their own performance statistics

❌ **Cannot:**
- Create new tasks
- Edit task details
- Delete tasks
- View other housekeepers' tasks
- Assign tasks to others

---

## Controller Structure

All routes use the **`HousekeepingController`** located at:
```
app/Http/Controllers/Tenant/HousekeepingController.php
```

### Required Controller Methods

#### Supervisor Methods:
- `index()` - List all tasks with filters
- `create()` - Show create form
- `store()` - Store new task
- `show()` - Show task details
- `edit()` - Show edit form
- `update()` - Update task
- `destroy()` - Delete task
- `updateStatus()` - Update status
- `assign()` - Assign to housekeeper(s)
- `markComplete()` - Mark as completed
- `createForDirtyRooms()` - Bulk create

#### Housekeeper Methods:
- `myTasks()` - List my tasks
- `todayTasks()` - Today's tasks
- `showTask()` - View task
- `startTask()` - Start task
- `completeTask()` - Complete task
- `updateTaskProgress()` - Update progress
- `myStatistics()` - View stats

---

## Views Directory Structure

```
resources/views/Users/tenant/housekeeping/
├── supervisor/
│   ├── index.blade.php       # Supervisor task list
│   ├── create.blade.php      # Create task form
│   ├── edit.blade.php        # Edit task form
│   └── show.blade.php        # Task details
└── housekeeper/
    ├── index.blade.php       # Housekeeper task list
    ├── today.blade.php       # Today's tasks
    ├── show.blade.php        # Task details
    └── statistics.blade.php  # Performance stats
```

---

## Database Table

**Table:** `housekeeping_tasks`

**Existing Columns:**
- `id` (UUID)
- `property_id` (UUID)
- `room_id` (UUID)
- `assigned_to` (UUID) - Single housekeeper assignment
- `task_type` (ENUM: DAILY_CLEAN, DEEP_CLEAN, TURNDOWN, INSPECTION, OTHER)
- `status` (ENUM: PENDING, IN_PROGRESS, COMPLETED, VERIFIED, CANCELLED)
- `priority` (ENUM: LOW, MEDIUM, HIGH)
- `notes` (TEXT)
- `scheduled_date` (DATE)
- `scheduled_time` (TIME)
- `started_at` (TIMESTAMP)
- `completed_at` (TIMESTAMP)
- `verified_at` (TIMESTAMP)
- `verified_by` (UUID)
- `created_by` (UUID)
- `updated_by` (UUID)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

**Note:** The current schema supports single housekeeper assignment via `assigned_to`. For multiple housekeeper assignment, a pivot table would be needed.

---

## Next Steps

1. ✅ Routes have been updated
2. 🔄 Update `HousekeepingController` methods to match new route structure
3. 🔄 Create/update Blade views following project UI patterns
4. 🔄 Add role-based middleware to ensure proper access control
5. 🔄 Test all routes and functionalities

---

## Notes

- All routes follow the existing project pattern (matching RoomsController, GuestController, etc.)
- Route methods use POST for starting/completing tasks (standard REST for actions)
- Supervisor routes use the main `/housekeeping` prefix
- Housekeeper routes use a separate `/housekeeper` prefix for clarity
- No model files were modified (as requested)
- SupervisorController references have been removed (consolidated into HousekeepingController)
