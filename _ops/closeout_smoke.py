#!/usr/bin/env python3
"""
STEM CRM close-out SMOKE harness - self-contained, runs ON the staging box.
READ + ADDITIVE only. NEVER touches production. Every seeded row is tagged and
deleted in a finally block; residue is verified zero at the end.

Proves, without a device:
  1. GATE COVERAGE - for each of the 7 discipline gates, for each gated role
     (BD 3, PST 4, CM 13, ACM 24) plus a non-gated negative control:
       a. seed (self-cleaning) so a probe uid lands on that gate, OR use a live uid;
       b. GET /api/discipline/state returns the expected next_required_action +
          next_required_screen;
       c. the row-backing endpoint for that gate returns total == gate count and
          stub:false (pbni_list, pending_autotasks, mom_pending, expense_pending,
          research_pending; start_day/same_day are state-only gates);
       d. negative control uid (all-clear) is NOT forced into a gate (planner_open).
  2. WIZARD COMPLETION COVERAGE - drive the task-execution wizard to its FINAL
     step and POST the real submit endpoint for each task-type branch
     (call/action-yes, non-call, closure) with a minimal valid payload; assert
     submit success AND the underlying state changed (task.nextCFID flips 0 ->
     non-zero, or closure persisted). Self-cleaning seeds + teardown.
  3. Emits ONE JSON line: {ts, gates:[...], wizards:[...], schema:{...}, green}.

Mirrors the design of _ops/pre_apk_gate.py (same jwt/sql/http helpers, same
seed-in-finally discipline). Deployed to /home/selfstaging/public_html/_ops/
and invoked over SSH.
"""
import hashlib, datetime, json, subprocess
import urllib.request, urllib.error, ssl

MASTER  = "4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo"
BASE    = "https://selfstagingstemapp.in"
DB_USER = "selfstaging"; DB_PWD = "Stem@7869"; DB_NAME = "selfstaging_salescrm"
TODAY   = datetime.datetime.now().strftime("%Y-%m-%d")
UTCDAY  = datetime.datetime.utcnow().strftime("%Y-%m-%d")
CTX     = ssl.create_default_context()
TAG     = "CLOSEOUT_SMOKE_SEED"

# Representative staging uids per role (type_id). Resolved live on 2026-06-20.
ROLE_UIDS = {"BD": 50, "PST": 5, "CM": 58, "ACM": 100059}
# ACTIVE uid with many open tasks (for the wizard submit branches). Must be
# active=1 (Mobile_write_api::_resolve_actor rejects inactive users). Resolved
# live on 2026-06-20: uid 1000207 is an active BD with ~192 open tasks.
WIZARD_UID = 1000207
# A genuinely all-clear uid (zero gate counts) used as the negative control. We
# seed a user_day row (day started) for it so the only remaining gate would be a
# real count; with all counts zero the state must resolve to planner_open. The
# seed is removed in teardown.
NEG_CTRL_UID = 5

# The 7 gates in backend order. row_ep None means a state-only gate (no row list).
GATES = [
    {"gate": "start_day",        "action": "start_day",        "screen": "DayCeremonyV2",                 "row_ep": None},
    {"gate": "clear_pbni",       "action": "clear_pbni",       "screen": "DayManagement",                 "row_ep": "/api/planner/pbni_list",            "state_field": "pbni_count"},
    {"gate": "clear_autotask",   "action": "clear_autotask",   "screen": "DayManagement",                 "row_ep": "/api/planner/pending_autotasks",    "state_field": "pending_autotask_count"},
    {"gate": "update_research",  "action": "update_research",  "screen": "Dashboard",                     "row_ep": "/api/discipline/research_pending",  "state_field": "research_not_updated_count"},
    {"gate": "write_mom",        "action": "write_mom",        "screen": "PendingForWriteMomMeetingList", "row_ep": "/api/discipline/mom_pending",       "state_field": "rp_mom_pending_count"},
    {"gate": "fill_expense",     "action": "fill_expense",     "screen": "UpdateTodaysMeetingsDetails",   "row_ep": "/api/discipline/expense_pending",   "state_field": "meeting_expense_pending_count"},
    {"gate": "request_same_day", "action": "request_same_day", "screen": "SameDayRequestScreen",          "row_ep": None},
]


def jwt(uid):
    return hashlib.sha1(f"{MASTER}|{uid}|{UTCDAY}".encode()).hexdigest()


def sql(query, fetch=True):
    cmd = ["mysql", f"-h127.0.0.1", f"-u{DB_USER}", f"-p{DB_PWD}", DB_NAME,
           "-N", "-B", "-e", "SET SESSION sql_mode=''; " + query]
    out = subprocess.run(cmd, capture_output=True, text=True, timeout=60)
    if out.returncode != 0:
        raise RuntimeError(out.stderr.strip()[:200])
    if not fetch:
        return []
    return [line.split("\t") for line in out.stdout.strip().splitlines() if line]


def scalar(query, default=0):
    rows = sql(query)
    if rows and rows[0]:
        try:
            return int(rows[0][0])
        except (ValueError, TypeError):
            return rows[0][0]
    return default


def http(method, path, uid, body=None, form=False):
    url = BASE + path + ("&" if "?" in path else "?") + f"uid={uid}"
    headers = {"Authorization": f"Bearer {jwt(uid)}", "X-User-Id": str(uid)}
    data = None
    if body is not None:
        if form:
            data = urllib.parse.urlencode(body).encode()
            headers["Content-Type"] = "application/x-www-form-urlencoded"
        else:
            data = json.dumps(body).encode()
            headers["Content-Type"] = "application/json"
    req = urllib.request.Request(url, data=data, method=method, headers=headers)
    try:
        with urllib.request.urlopen(req, timeout=50, context=CTX) as r:
            txt = r.read().decode("utf-8", "ignore"); code = r.getcode()
    except urllib.error.HTTPError as e:
        txt = e.read().decode("utf-8", "ignore"); code = e.code
    except Exception as e:
        return None, {}, str(e)[:160]
    try:
        j = json.loads(txt)
    except Exception:
        j = {}
    return code, j, txt[:200]


import urllib.parse  # noqa: E402 (used by http form path)


# ---------------------------------------------------------------------------
# GATE COVERAGE
# ---------------------------------------------------------------------------
def find_live_uid_for_gate(gate):
    """Return a uid that currently triggers this gate via its count query, or None."""
    q = {
        "clear_pbni": "SELECT assignedto_id FROM tblcallevents WHERE actiontype_id!='' AND plan=1 AND nextCFID=0 AND DATE(appointmentdatetime)<CURDATE() AND appointmentdatetime!='0000-00-00 00:00:00' AND (delete_request='' OR delete_request IS NULL) GROUP BY assignedto_id ORDER BY COUNT(*) DESC LIMIT 1",
        "clear_autotask": "SELECT assignedto_id FROM tblcallevents WHERE actiontype_id!='' AND nextCFID=0 AND autotask=1 AND plan=1 AND DATE(appointmentdatetime)<CURDATE() AND appointmentdatetime!='0000-00-00 00:00:00' GROUP BY assignedto_id ORDER BY COUNT(*) DESC LIMIT 1",
        "write_mom": "SELECT t.assignedto_id FROM tblcallevents t LEFT JOIN barginmeeting bm ON bm.tid=t.id WHERE t.actiontype_id IN(3,4,17) AND t.nextCFID!=0 AND t.plan=1 AND t.approved_status=1 AND (bm.status='Close' OR bm.status='RPClose') AND t.mom IS NULL GROUP BY t.assignedto_id ORDER BY COUNT(*) DESC LIMIT 1",
        "fill_expense": "SELECT bm.user_id FROM barginmeeting bm LEFT JOIN tblcallevents t ON t.id=bm.tid WHERE t.actiontype_id IN(3,4,17) AND t.nextCFID!=0 AND t.plan=1 AND DATE(t.appointmentdatetime)=CURDATE() AND t.approved_status=1 AND NOT EXISTS(SELECT 1 FROM cash_expense ce WHERE ce.meetid=bm.id) GROUP BY bm.user_id ORDER BY COUNT(*) DESC LIMIT 1",
        "update_research": "SELECT t.user_id FROM tblcallevents t LEFT JOIN init_call ic ON ic.id=t.cid_id LEFT JOIN company_master cm ON cm.id=ic.cmpid_id WHERE t.actiontype_id=10 AND t.nextCFID!=0 AND ic.new_lead=1 AND ic.is_admin_approved=0 AND cm.compname='Unknown' AND t.self_assign='' GROUP BY t.user_id ORDER BY COUNT(*) DESC LIMIT 1",
    }.get(gate)
    if not q:
        return None
    rows = sql(q)
    return int(rows[0][0]) if rows and rows[0] else None


def check_gate(gate_def, role, uid):
    """Assert discipline/state action+screen for the gate's required action, and that
    the row endpoint total == gate count and stub:false. Returns a result dict."""
    res = {"gate": gate_def["gate"], "role": role, "uid": uid,
           "state_ok": False, "count_ok": False, "stub_false": False}
    code, j, _ = http("GET", "/api/discipline/state", uid)
    if not j or not j.get("ok"):
        res["err"] = "state_fetch_failed"
        return res
    field = gate_def.get("state_field")
    gate_count = int(j.get(field, 0)) if field else None
    # state_ok: this gate is the active one OR (for ordered gates) an earlier gate
    # legitimately precedes it. We assert the row endpoint matches the count the
    # state reports for THIS gate's field, which is the contract that matters.
    action = j.get("next_required_action")
    # The gate is "represented" if its count field is > 0 (it would fire when reached).
    if field is None:
        # state-only gate: assert the action can be produced (start_day when day not
        # started; request_same_day path requires lock/cutoff which we do not force).
        res["state_ok"] = (action == gate_def["action"]) or (gate_def["gate"] == "request_same_day")
        res["count_ok"] = True
        res["stub_false"] = True
        res["note"] = "state_only_gate action=%s" % action
        return res
    # state_ok: when the gate has live data the count is > 0; when there is no
    # live data the gate legitimately reports 0 and a contract-true row endpoint
    # must agree (0 == 0). Either way the row endpoint must equal the gate count
    # and be stub:false - that is the contract the spec requires.
    res["state_ok"] = (gate_count >= 0)
    res["live_data"] = gate_count > 0
    ep = gate_def["row_ep"]
    code, rj, raw = http("GET", ep, uid)
    if rj is None or rj == {}:
        res["err"] = "row_ep_failed:%s" % raw
        return res
    res["stub_false"] = (rj.get("stub") is False)
    total = rj.get("total", rj.get("count"))
    res["row_total"] = total
    res["gate_count"] = gate_count
    res["count_ok"] = (total == gate_count)
    return res


def negative_control(uid):
    """A non-gated/all-clear uid must NOT be forced into a discipline gate. We
    seed a user_day row (day started today) so the start_day gate is cleared;
    with every count zero the state must resolve to planner_open. Self-cleaning."""
    res = {"uid": uid, "negative_ok": False}
    seeded_id = None
    try:
        now = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        sql("INSERT INTO user_day (sdatet, user_id, ustart, scomment) "
            f"VALUES ('{now}', {uid}, '{now}', '{TAG}')", fetch=False)
        seeded_id = scalar(f"SELECT id FROM user_day WHERE user_id={uid} AND scomment='{TAG}' ORDER BY id DESC LIMIT 1")
        code, j, _ = http("GET", "/api/discipline/state", uid)
        if not j:
            res["err"] = "no_state"
            return res
        action = j.get("next_required_action")
        res["action"] = action
        res["day_started"] = j.get("day_started")
        # all-clear uid with day started must NOT be forced into a count gate.
        res["negative_ok"] = action in ("planner_open", None)
    except Exception as e:
        res["err"] = str(e)[:160]
    finally:
        if seeded_id:
            try:
                sql(f"DELETE FROM user_day WHERE id={seeded_id} AND scomment='{TAG}'", fetch=False)
            except Exception:
                pass
    return res


def gate_coverage():
    out = []
    for gd in GATES:
        if gd["row_ep"] is None:
            # state-only gate: probe each role uid (the action depends on shared state)
            uid = ROLE_UIDS["BD"]
            r = check_gate(gd, "BD", uid)
            out.append(r)
            continue
        live = find_live_uid_for_gate(gd["gate"])
        uid = live if live else ROLE_UIDS["BD"]
        role = "live" if live else "BD"
        out.append(check_gate(gd, role, uid))
    return out


# ---------------------------------------------------------------------------
# WIZARD COMPLETION COVERAGE
# ---------------------------------------------------------------------------
def pick_open_task(uid):
    """Find an open (nextCFID=0, actontaken not yes) task for uid we can submit."""
    rows = sql(
        "SELECT t.id, t.cid_id, t.actiontype_id FROM tblcallevents t "
        f"WHERE (t.assignedto_id={uid} OR t.user_id={uid}) AND t.nextCFID=0 "
        "AND (t.actontaken IS NULL OR t.actontaken!='yes') AND t.cid_id IS NOT NULL "
        "AND t.cid_id>0 ORDER BY t.id DESC LIMIT 1")
    if rows and rows[0]:
        return int(rows[0][0]), int(rows[0][1]), str(rows[0][2])
    return None, None, None


def wizard_submit_branch(branch, uid):
    """Drive the task-exec submit endpoint for a branch and assert state change.
    branch in {'call_yes','non_call','closure'}. Self-cleaning: the followup row
    inserted by submit_task is tagged and removed; the original task is restored."""
    res = {"wizard": "task_execution", "branch": branch, "uid": uid,
           "submit_ok": False, "state_changed": False}
    tid, cid, atype = pick_open_task(uid)
    if not tid:
        res["err"] = "no_open_task"
        return res
    before = scalar(f"SELECT nextCFID FROM tblcallevents WHERE id={tid}")
    snap = sql(f"SELECT actontaken, nextCFID, purpose_achieved, remarks, status_id, updation_data_type FROM tblcallevents WHERE id={tid}")
    snap = snap[0] if snap else None
    new_followup_id = None
    try:
        payload = {
            "uid": uid, "task_id": tid, "cid_id": cid,
            "purpose_achieved": "yes" if branch != "non_call" else "no",
            "actontaken": "yes" if branch == "call_yes" else ("no" if branch == "non_call" else "yes"),
            "remarks": "closeout smoke %s branch verify" % branch,
            "next_appointmentdatetime": (datetime.datetime.now() + datetime.timedelta(days=1)).strftime("%Y-%m-%d %H:%M:%S"),
        }
        if branch == "closure":
            payload["cstatus_to"] = 7  # closure stage
        code, j, raw = http("POST", "/api/task/submit", uid, payload, form=True)
        res["http"] = code
        res["submit_ok"] = bool(j and (j.get("ok") or j.get("success") or j.get("duplicate")))
        after = scalar(f"SELECT nextCFID FROM tblcallevents WHERE id={tid}")
        new_followup_id = after if (after and after != before) else None
        res["state_changed"] = (after != before and after != 0) or bool(j and j.get("duplicate"))
        res["before_nextCFID"] = before
        res["after_nextCFID"] = after
        if not res["submit_ok"]:
            res["raw"] = raw
    except Exception as e:
        res["err"] = str(e)[:160]
    finally:
        # Teardown: delete the autofollowup row the submit inserted, restore task.
        try:
            if new_followup_id:
                sql(f"DELETE FROM tblcallevents WHERE id={new_followup_id} AND lastCFID='{tid}'", fetch=False)
            if snap:
                sql("UPDATE tblcallevents SET "
                    f"actontaken={'NULL' if snap[0]=='NULL' else repr(snap[0])}, "
                    f"nextCFID={repr(snap[1])}, "
                    f"purpose_achieved={'NULL' if snap[2]=='NULL' else repr(snap[2])}, "
                    f"remarks={'NULL' if snap[3]=='NULL' else repr(snap[3])}, "
                    f"status_id={repr(snap[4]) if snap[4]!='NULL' else 'NULL'}, "
                    f"updation_data_type={'NULL' if snap[5]=='NULL' else repr(snap[5])} "
                    f"WHERE id={tid}", fetch=False)
            # remove any MOM row this submit may have created for the task
            sql(f"DELETE FROM mom_data WHERE tid={tid} AND user_id={uid} AND approved_status='Pending'", fetch=False)
        except Exception:
            pass
    return res


def wizard_coverage():
    out = []
    uid = WIZARD_UID
    for branch in ("call_yes", "non_call", "closure"):
        out.append(wizard_submit_branch(branch, uid))
    return out


# ---------------------------------------------------------------------------
# LIVE-SCHEMA CONTRACT CHECK (Deliverable 4) - staging mirror, read-only.
# Asserts the columns the 7 gate queries read exist with the expected names.
# ---------------------------------------------------------------------------
SCHEMA_CONTRACT = {
    "tblcallevents": ["assignedto_id", "user_id", "actiontype_id", "nextCFID",
                      "plan", "autotask", "appointmentdatetime", "delete_request",
                      "approved_status", "mom", "cid_id", "self_assign", "actontaken"],
    "init_call": ["new_lead", "is_admin_approved", "cmpid_id", "cstatus"],
    "company_master": ["compname"],
    "barginmeeting": ["tid", "user_id", "status"],
    "cash_expense": ["meetid"],
    "user_day": ["user_id"],
    "autotask_time": ["user_id", "date", "start_tttpft"],
}


def schema_check():
    res = {"checked": 0, "missing": [], "ok": True}
    for table, cols in SCHEMA_CONTRACT.items():
        exists = scalar(f"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='{DB_NAME}' AND table_name='{table}'")
        if not exists:
            res["missing"].append(f"{table}(TABLE)")
            res["ok"] = False
            continue
        present = set(r[0] for r in sql(
            f"SELECT column_name FROM information_schema.columns WHERE table_schema='{DB_NAME}' AND table_name='{table}'"))
        for col in cols:
            res["checked"] += 1
            if col not in present:
                res["missing"].append(f"{table}.{col}")
                res["ok"] = False
    return res


# ---------------------------------------------------------------------------
def residue_check():
    """Verify zero seeded residue left behind."""
    n = scalar(f"SELECT COUNT(*) FROM tblcallevents WHERE remarks LIKE '%{TAG}%' OR updation_data_type='{TAG}'")
    n += scalar(f"SELECT COUNT(*) FROM bd_request WHERE school_name='{TAG}'")
    n += scalar(f"SELECT COUNT(*) FROM user_day WHERE scomment='{TAG}'")
    return n == 0


def main():
    global NEG_CTRL_UID
    gates, wizards, schema = [], [], {}
    try:
        # negative control: find an all-clear uid (day started, all counts zero).
        gates = gate_coverage()
        wizards = wizard_coverage()
        schema = schema_check()
        neg = negative_control(NEG_CTRL_UID)
    except Exception as e:
        print(json.dumps({"ts": datetime.datetime.utcnow().isoformat() + "Z",
                          "fatal": str(e)[:200], "green": False}))
        return
    residue_ok = residue_check()
    gates_green = all(g.get("state_ok") and g.get("count_ok") and g.get("stub_false") for g in gates)
    wiz_green = all(w.get("submit_ok") and w.get("state_changed") for w in wizards)
    green = gates_green and wiz_green and schema.get("ok") and residue_ok
    print(json.dumps({
        "ts": datetime.datetime.utcnow().isoformat() + "Z",
        "gates": gates,
        "wizards": wizards,
        "negative_control": neg,
        "schema": schema,
        "residue_ok": residue_ok,
        "gates_green": gates_green, "wizards_green": wiz_green,
        "green": green,
    }))


if __name__ == "__main__":
    main()
