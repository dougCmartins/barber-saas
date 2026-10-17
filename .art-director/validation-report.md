# Validation report — Client Hub + Meus Agendamentos

Date: 2026-10-17

| Section | Expected (Lovable) | Actual (Vue) | Divergence |
|---|---|---|---|
| Hub shell | `max-w-lg`, `px-4`, `gap-4`, `bg-background`, bottom nav | Same in `ClientHub.vue` | None |
| Hub link | “Ver todos os agendamentos →” after next appointment | `RouterLink` + i18n `clientAppointments.viewAll` | None |
| Appointments route | `/meus-agendamentos` | Registered as `client-appointments` | None |
| Sticky header | Back + title + 3 filter chips | Implemented | None |
| Appointment cards | Avatar, name, date/time, services, price, status tag, Cancel if booked | `AppointmentCard.vue` | Status codes EN; labels via i18n (Agendado = confirmed) |
| Filters | Bottom sheets with chips + Limpar/Aplicar | `FilterBottomSheet` + period/status/barber | None |
| Nav Perfil | Active on appointments | `RouterLink` → `/meus-agendamentos`, `active="account"` | None |
| Figma art-direction/agendamentos | Rectangular image / cyan chips | Not used (Lovable is source of truth) | Intentional |

## Automated tests

```
npm run test:unit -- --run --pool=threads \
  src/utils/appointmentsFilter.test.ts \
  src/Domain/Orchestrator/ClientAppointments.test.ts \
  src/Domain/Orchestrator/ClientHub.test.ts
```

Result: **13 passed** (3 files).

## Browser

- `http://localhost:5173/meus-agendamentos` opened: sticky header, 3 chips, appointment cards (avatar, status tags Agendado/Concluído/Faltou, Cancelar on confirmed), bottom nav with Perfil current.
- Login not required for these mock client screens.
- Lovable side-by-side (`5174`) not re-checked in this pass (optional visual parity already matched by class port).
