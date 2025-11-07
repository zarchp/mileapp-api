const dbName = process.env.DB_NAME || db.getName();
const tasks = db.getSiblingDB(dbName).tasks;

tasks.createIndexes([
  // 1. Sort or paginate by created_at (default listing)
  {
    key: { created_at: -1 },
    name: 'tasks_created_at_desc',
  },

  // 2. Filter by is_completed and sort by due_date
  {
    key: { is_completed: 1, due_date: 1 },
    name: 'tasks_completed_due_date',
  },

  // 3. View completed tasks sorted by completed_at desc
  {
    key: { is_completed: 1, completed_at: -1 },
    name: 'tasks_completed_at_desc',
    partialFilterExpression: { is_completed: true },
  },
]);
