<?php
// delete_proposal.php - remove a proposal and its history (Head/Admin only)
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Auth required']); exit; }
$role = $_SESSION['user']['role'] ?? '';
if (!preg_match('/head|admin/i',$role)) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Forbidden']); exit; }
$id = (int)($_POST['id'] ?? 0);
if ($id<=0){ http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); exit; }
$mysqli = require dirname(__DIR__) . '/db.php';
// delete history first
try { $h = $mysqli->prepare('DELETE FROM proposal_history WHERE proposal_id = ?'); if ($h){ $h->bind_param('i',$id); @$h->execute(); $h->close(); } } catch (Exception $e) {}
// delete analytics/map entries if exist
try { $a = $mysqli->prepare('DELETE FROM proposal_analytics WHERE proposal_id = ?'); if ($a){ $a->bind_param('i',$id); @$a->execute(); $a->close(); } } catch (Exception $e) {}
try { $m = $mysqli->prepare('DELETE FROM proposal_container_map WHERE proposal_id = ?'); if ($m){ $m->bind_param('i',$id); @$m->execute(); $m->close(); } } catch (Exception $e) {}
// finally delete proposal
$stmt = $mysqli->prepare('DELETE FROM project_proposals WHERE id = ? LIMIT 1');
if (!$stmt){ http_response_code(500); echo json_encode(['success'=>false,'error'=>'Prepare failed']); exit; }
$stmt->bind_param('i',$id);
if(!$stmt->execute()){ http_response_code(500); echo json_encode(['success'=>false,'error'=>'Execute failed']); exit; }
$stmt->close();

echo json_encode(['success'=>true,'deleted_id'=>$id]);
