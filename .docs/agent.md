# Agent Instruction — Online Shop Platform

## Technology Stack Used

- laravel 11
- laravel sail
- mysql
- redis
- inertia
- nvm use 22 for node version 22
- frontend vite & vue 3

## Database Awareness

Before generating any UI:

1. Read schema database/schema/mysql-schema.sql

2. Understand:
   - products table
   - product_variants table
   - categories table
   - orders table
   - order_items table
   - carts table
   - users table
   - shipping related tables (if exist)

3. Detect:
   - Column names
   - Data types
   - Foreign keys
   - Relationships

4. DO NOT invent fields that do not exist in migrations.

5. If a required field is missing, ask before proceeding.