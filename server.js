// server.js - Minimal backend
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// Mock database
let items = [];

// Routes
app.get('/api/items', (req, res) => {
  res.json(items);
});

app.post('/api/items', (req, res) => {
  const newItem = { ...req.body, id: Date.now() };
  items.push(newItem);
  res.status(201).json(newItem);
});

// Start server
const PORT = process.env.PORT || 443;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});