<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private const FINISHED = ["delivered", "cancelled"];

    public function index(Request $request)
    {
        $q = trim((string) $request->query("q", ""));
        $status = $request->query("status");

        $trips = Schedule::with(["driver.user", "truck"])
            ->whereIn("status", self::FINISHED)
            ->when($q !== "", function ($query) use ($q) {
                $like = "%" . $q . "%";
                $query->where(function ($w) use ($q, $like) {
                    $w->where("origin_address", "ilike", $like)
                        ->orWhere("destination_address", "ilike", $like)
                        ->orWhereHas(
                            "truck",
                            fn($t) => $t->where("plate_no", "ilike", $like),
                        )
                        ->orWhereHas(
                            "driver.user",
                            fn($u) => $u->where("name", "ilike", $like),
                        );

                    if (preg_match('/^(fp-?)?(\d+)$/i', $q, $m)) {
                        $w->orWhere("id", (int) $m[2]);
                    }
                });
            })
            ->when(
                in_array($status, self::FINISHED, true),
                fn($query) => $query->where("status", $status),
            )
            ->latest("updated_at")
            ->paginate(8)
            ->withQueryString();

        $selected = null;
        $tripId = (int) $request->query("trip", 0);

        if ($tripId > 0) {
            $selected = Schedule::whereIn("status", self::FINISHED)->find(
                $tripId,
            );
        }

        $selected ??= $trips->first();
        $selected?->load(["driver.user", "truck", "statusLogs.loggedBy"]);

        return view(
            "transactions.index",
            compact("trips", "selected", "q", "status"),
        );
    }
}
