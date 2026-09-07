---
schema: kumwe-migration-handoff/v2
artifact_kind: framework_php
migration_id: KUMWE-MIG-2026-004
change_set: KUMWE-CS-2026-004
state: draft_pr_open
source:
  app:
    repository: https://github.com/kumwe/app
    baseline_commit: 960ce8ec00cf724a7cae03e5ba09c4852c9ab54e
    examined_paths:
      - src/Application/Authorization/AuthenticatedSurface.php
      - src/Application/Authorization/AuthenticationStrength.php
      - src/Application/Authorization/ExecutionContext.php
      - src/Application/Authorization/MembershipContext.php
      - src/Application/Authorization/OrganizationContext.php
      - src/Application/Authorization/SiteContext.php
      - src/Application/Authorization/StepUpProof.php
      - src/Application/Authorization/WorkspaceContext.php
    old_namespace_roots:
      - Kumwe\App\Application\Authorization\
    capability_index_sha256: null
  semantic_inputs: []
  examined_dependencies:
    - "PHP 8.5: no Kumwe runtime dependency required."
    - "App composer.lock and governance legacy inventory; existing identity and SDK boundaries."
  active_related_pull_requests:
    - https://github.com/kumwe/access-context/pull/3
target:
  repository: https://github.com/kumwe/access-context
  artifact_identity: kumwe/access-context
  canonical_namespace_or_abi: Kumwe\Context
  branch: codex/extraction-readiness-20260907
  pull_request: https://github.com/kumwe/access-context/pull/8
ownership:
  responsibility: "Immutable explicitly supplied actor, scope, authentication and execution facts."
  non_responsibilities:
    - "Authentication, authorization, capability/grant policy, credentials and session resolution."
    - "Membership lookup, trusted system identity admission, persistence, delivery and ambient context."
  allowed_dependency_ceiling: []
  implementation_owner: kumwe/access-context
  next_consumer: kumwe/app
  public_manifests:
    - path: resources/public-api/v1.json
      sha256: 109b5449f343a28a2ab523c7460d7b7c4ee6d1c109f2cdaf96dd0d2124e681ba
    - path: resources/capabilities/v1.json
      sha256: fcd9597350e055c32b33ea442f51b952ba2621776a16069fa5ece318c909ea02
    - path: resources/service-map/v1.json
      sha256: 7c7647997a28ad0cdb947ceda65e8391f6c13426a1278b25b458577b23e83c63
  intentionally_excluded:
    - "AuthenticatedPrincipal and SystemIdentity stay App-owned; implement the new neutral ports there."
    - "MembershipContextValidator, grants, capabilities, request attribute and SDK adapter remain App-owned."
framework_php:
  composer_package: kumwe/access-context
  canonical_namespace: Kumwe\Context
  public_api_manifest: resources/public-api/v1.json
  capability_manifest: resources/capabilities/v1.json
  service_map: resources/service-map/v1.json
  extracted_symbols:
    - old_fqcn: Kumwe\App\Application\Authorization\AuthenticatedSurface
      new_fqcn: Kumwe\Context\Value\AuthenticatedSurface
      source_path: src/Application/Authorization/AuthenticatedSurface.php
      target_path: src/Value/AuthenticatedSurface.php
      kind: enum
      public_methods: []
      public_properties: []
      public_constants:
        - Administrator
        - Api
        - Background
        - Cli
        - Mcp
        - Portal
        - Recovery
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Preserved with additive equality/export helpers."
    - old_fqcn: Kumwe\App\Application\Authorization\AuthenticationStrength
      new_fqcn: Kumwe\Context\Value\AuthenticationStrength
      source_path: src/Application/Authorization/AuthenticationStrength.php
      target_path: src/Value/AuthenticationStrength.php
      kind: enum
      public_methods:
        - isHuman
        - satisfies
      public_properties: []
      public_constants:
        - BearerToken
        - MultiFactor
        - Password
        - System
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Preserved with additive equality/export helpers."
    - old_fqcn: Kumwe\App\Application\Authorization\ExecutionContext
      new_fqcn: Kumwe\Context\Value\ExecutionContext
      source_path: src/Application/Authorization/ExecutionContext.php
      target_path: src/Value/ExecutionContext.php
      kind: class
      public_methods:
        - actorId
        - approvalFingerprint
        - authenticationStrength
        - authorizationFingerprint
        - child
        - correlationId
        - deliverySurface
        - hasProvenance
        - isSystem
        - issueHuman
        - issueSystem
        - membership
        - organization
        - organizationIdentifier
        - principal
        - requestId
        - sessionId
        - site
        - siteIdentifier
        - stepUpProof
        - surface
        - systemActor
        - toArray
        - workspace
        - workspaceIdentifier
      public_properties: []
      public_constants: []
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Neutral actor ports replace App identity types; App request/SDK adapters remain host-owned."
    - old_fqcn: Kumwe\App\Application\Authorization\MembershipContext
      new_fqcn: Kumwe\Context\Value\MembershipContext
      source_path: src/Application/Authorization/MembershipContext.php
      target_path: src/Value/MembershipContext.php
      kind: class
      public_methods:
        - __construct
        - equals
        - fingerprint
        - membershipId
        - membershipVersion
        - organization
        - policyGeneration
        - toArray
        - workspace
      public_properties: []
      public_constants: []
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Preserved with additive equality/export helpers."
    - old_fqcn: Kumwe\App\Application\Authorization\OrganizationContext
      new_fqcn: Kumwe\Context\Value\OrganizationContext
      source_path: src/Application/Authorization/OrganizationContext.php
      target_path: src/Value/OrganizationContext.php
      kind: class
      public_methods:
        - equals
        - fromString
        - identifier
      public_properties: []
      public_constants: []
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Reject raw control bytes before normalization; preserve canonical identifiers."
    - old_fqcn: Kumwe\App\Application\Authorization\SiteContext
      new_fqcn: Kumwe\Context\Value\SiteContext
      source_path: src/Application/Authorization/SiteContext.php
      target_path: src/Value/SiteContext.php
      kind: class
      public_methods:
        - default
        - equals
        - fromString
        - identifier
      public_properties: []
      public_constants:
        - DEFAULT
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Reject raw control bytes before normalization; preserve canonical identifiers."
    - old_fqcn: Kumwe\App\Application\Authorization\StepUpProof
      new_fqcn: Kumwe\Context\Value\StepUpProof
      source_path: src/Application/Authorization/StepUpProof.php
      target_path: src/Value/StepUpProof.php
      kind: class
      public_methods:
        - __construct
        - actorId
        - expiresAt
        - isValidFor
        - method
        - nonce
        - organization
        - purpose
        - securityEpoch
        - sessionId
        - site
        - toArray
        - verifiedAt
        - workspace
      public_properties: []
      public_constants: []
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Reject orphan workspace; retain signatures and documented freshness/fingerprint semantics."
    - old_fqcn: Kumwe\App\Application\Authorization\WorkspaceContext
      new_fqcn: Kumwe\Context\Value\WorkspaceContext
      source_path: src/Application/Authorization/WorkspaceContext.php
      target_path: src/Value/WorkspaceContext.php
      kind: class
      public_methods:
        - equals
        - fromString
        - identifier
      public_properties: []
      public_constants: []
      exceptions:
        - Kumwe\Context\Exception\InvalidContext
      serialization_contract: "Explicit redacted exports; preserve established fingerprints and scalar vocabulary."
      compatibility: "Reject raw control bytes before normalization; preserve canonical identifiers."
  consumers:
    app_code:
      - src/Administrator/Http/AdministratorRequest.php
      - src/Administrator/Http/Handler/AdministratorAccessControlHandler.php
      - src/Administrator/Http/Handler/AdministratorAutomationHandler.php
      - src/Administrator/Http/Handler/AdministratorBusinessSecurityHandler.php
      - src/Administrator/Http/Handler/AdministratorDashboardHandler.php
      - src/Administrator/Http/Handler/AdministratorLoginHandler.php
      - src/Administrator/Http/Handler/AdministratorNavigationHandler.php
      - src/Administrator/Http/Middleware/AdministratorSessionMiddleware.php
      - src/Administrator/Presentation/AdministratorContributionRenderer.php
      - src/Application/Authorization/AuthenticatedSurface.php
      - src/Application/Authorization/AuthenticationStrength.php
      - src/Application/Authorization/AuthorizationDecisionRecorder.php
      - src/Application/Authorization/AuthorizationGateway.php
      - src/Application/Authorization/DenyByDefaultAuthorizationGateway.php
      - src/Application/Authorization/ExecutionContext.php
      - src/Application/Authorization/MembershipContext.php
      - src/Application/Authorization/MembershipContextValidator.php
      - src/Application/Authorization/OrganizationContext.php
      - src/Application/Authorization/OwnershipScope.php
      - src/Application/Authorization/ResourceOwnershipScopeService.php
      - src/Application/Authorization/ResourceSiteOwnershipWriter.php
      - src/Application/Authorization/SiteContext.php
      - src/Application/Authorization/SiteGroup.php
      - src/Application/Authorization/SiteGroupAdministration.php
      - src/Application/Authorization/SiteGroupWriter.php
      - src/Application/Authorization/StepUpProof.php
      - src/Application/Authorization/StructuredLogAuthorizationDecisionRecorder.php
      - src/Application/Authorization/SystemIdentity.php
      - src/Application/Authorization/SystemPrincipal.php
      - src/Application/Authorization/WorkspaceContext.php
      - src/Application/Automation/AutomationManagementService.php
      - src/Application/Automation/GlobalJobPrincipals.php
      - src/Application/Automation/Job/EnforceAuditRetentionHandler.php
      - src/Application/Automation/Job/PurgeAdministratorSessionsHandler.php
      - src/Application/Automation/Job/PurgeBusinessRecordIdempotencyHandler.php
      - src/Application/Automation/Job/PurgeIdempotencyRecordsHandler.php
      - src/Application/Automation/Job/PurgeStudioContentAuthoringContextsHandler.php
      - src/Application/Automation/Job/RebuildExtensionMapHandler.php
      - src/Application/Automation/Job/RecordAuditAnchorHandler.php
      - src/Application/Automation/Job/RotateRecordSecretsHandler.php
      - src/Application/Automation/Job/ScheduleRepository.php
      - src/Application/Automation/Job/SynchronizeTrustRevocationsHandler.php
      - src/Application/Automation/Job/TransitionContentHandler.php
      - src/Application/Automation/Job/VerifyAuditTrailHandler.php
      - src/Application/Automation/JobHandler.php
      - src/Application/Automation/JobQueue.php
      - src/Application/Automation/LeaseAwareJobHandler.php
      - src/Application/Automation/QueueRuntimeOperations.php
      - src/Application/Automation/Scheduler.php
      - src/Application/Automation/Worker.php
      - src/Application/Operations/MigrationLockRecoveryService.php
      - src/Application/Presentation/Dashboard/DashboardPreferenceService.php
      - src/Application/Presentation/Preference/PresentationAccessGroupRepository.php
      - src/Application/Presentation/Preference/PresentationPreferenceManager.php
      - src/Application/Security/HighImpactCredentialGuard.php
      - src/Audit/Application/AuditAnchorWriter.php
      - src/Audit/Application/AuditRetentionService.php
      - src/Audit/Application/AuditTrailExporter.php
      - src/Audit/Application/AuditTrailVerifier.php
      - src/Audit/Infrastructure/Persistence/DoctrineAuditAnchorWriter.php
      - src/Audit/Infrastructure/Persistence/DoctrineAuditRetentionService.php
      - src/Audit/Infrastructure/Persistence/DoctrineAuditTrailExporter.php
      - src/Audit/Infrastructure/Persistence/DoctrineAuditTrailVerifier.php
      - src/BusinessDefinition/Administrator/BusinessDefinitionFormMapper.php
      - src/BusinessDefinition/Application/BusinessDefinitionContractAdmission.php
      - src/BusinessDefinition/Application/BusinessDefinitionRepository.php
      - src/BusinessDefinition/Application/BusinessDefinitionService.php
      - src/BusinessDefinition/Application/PackageDefinitionSynchronizer.php
      - src/BusinessDefinition/Delivery/Api/BusinessDefinitionApiHandler.php
      - src/BusinessDefinition/Infrastructure/Persistence/DoctrineBusinessDefinitionRepository.php
      - src/BusinessDefinition/Infrastructure/Persistence/DoctrinePackageDefinitionSynchronizer.php
      - src/BusinessIntegration/Application/BusinessRecordMutationEventPublisher.php
      - src/BusinessIntegration/Application/IntegrationEventConsumerDispatcher.php
      - src/BusinessIntegration/Application/IntegrationOperationsService.php
      - src/BusinessIntegration/Application/JobQueueIntegrationEventHandler.php
      - src/BusinessIntegration/Application/JobQueueProcessWorkHandler.php
      - src/BusinessIntegration/Application/ProcessWorkDispatcher.php
      - src/BusinessIntegration/Application/ProcessWorkHandler.php
      - src/BusinessIntegration/Application/ProcessWorkLease.php
      - src/BusinessIntegration/Application/ValidatedContributedJobHandler.php
      - src/BusinessIntegration/Infrastructure/RuntimeIntegrationEventTransport.php
      - src/BusinessRecord/Application/BusinessRecordDefinitionResolver.php
      - src/BusinessRecord/Application/BusinessRecordMutationFence.php
      - src/BusinessRecord/Application/BusinessRecordMutationPublication.php
      - src/BusinessRecord/Application/BusinessRecordRelationshipCoordinator.php
      - src/BusinessRecord/Application/BusinessRecordService.php
      - src/BusinessRecord/Application/Command/ArchiveRecordCommand.php
      - src/BusinessRecord/Application/Command/CreateRecordCommand.php
      - src/BusinessRecord/Application/Command/DeleteRecordCommand.php
      - src/BusinessRecord/Application/Command/ExecuteRecordActionCommand.php
      - src/BusinessRecord/Application/Command/RelateRecordsCommand.php
      - src/BusinessRecord/Application/Command/ReorderRecordLinesCommand.php
      - src/BusinessRecord/Application/Command/RestoreRecordCommand.php
      - src/BusinessRecord/Application/Command/UnrelateRecordsCommand.php
      - src/BusinessRecord/Application/Command/UpdateRecordCommand.php
      - src/BusinessRecord/Application/Command/WriteDocumentCommand.php
      - src/BusinessRecord/Application/InstalledBusinessRecordDefinitionResolver.php
      - src/BusinessRecord/Application/PolicyBusinessRecordReader.php
      - src/BusinessRecord/Application/PostingPeriodService.php
      - src/BusinessRecord/Application/Query/BrowseOwnedLineFieldChoicesQuery.php
      - src/BusinessRecord/Application/Query/BrowseRecordsQuery.php
      - src/BusinessRecord/Application/Query/BrowseRelatedRecordsQuery.php
      - src/BusinessRecord/Application/Query/OwnedLineFormQuery.php
      - src/BusinessRecord/Application/Query/ReadRecordQuery.php
      - src/BusinessRecord/Application/Query/RecordHistoryQuery.php
      - src/BusinessRecord/Application/RecordSecretRotation.php
      - src/BusinessRecord/Domain/RecordScope.php
      - src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordMutationFence.php
      - src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordQueryCompiler.php
      - src/BusinessRecord/Infrastructure/Persistence/DoctrineBusinessRecordReadRepository.php
      - src/BusinessRecord/Infrastructure/Persistence/DoctrineRecordSecretRotation.php
      - src/BusinessReporting/Application/BusinessRecordReportReader.php
      - src/BusinessReporting/Application/ConsolidatedGroupReportScope.php
      - src/BusinessReporting/Application/ExportExecutionContextResolver.php
      - src/BusinessReporting/Application/ExportGenerationService.php
      - src/BusinessReporting/Application/ExportJobDispatcher.php
      - src/BusinessReporting/Application/ExportPolicySnapshotProvider.php
      - src/BusinessReporting/Application/ExportQueueProducerContextProvider.php
      - src/BusinessReporting/Application/ExportService.php
      - src/BusinessReporting/Application/GenerateReportExportHandler.php
      - src/BusinessReporting/Application/RecordExportReportProvider.php
      - src/BusinessReporting/Application/ReportExecutionRequest.php
      - src/BusinessReporting/Application/ReportScopeResolver.php
      - src/BusinessReporting/Application/ReportService.php
      - src/BusinessReporting/Delivery/Administrator/AdministratorReportHandler.php
      - src/BusinessReporting/Delivery/Api/ReportApiPresenter.php
      - src/BusinessReporting/Delivery/Portal/PortalReportHandler.php
      - src/BusinessReporting/Domain/ExportArtifact.php
      - src/BusinessReporting/Infrastructure/BusinessRecordExportPolicySnapshotProvider.php
      - src/BusinessReporting/Infrastructure/BusinessRecordReportScopeResolver.php
      - src/BusinessReporting/Infrastructure/BusinessRecordServiceReportReader.php
      - src/BusinessReporting/Infrastructure/JobQueueExportJobDispatcher.php
      - src/BusinessReporting/Infrastructure/LiveExportExecutionContextResolver.php
      - src/BusinessReporting/Infrastructure/SystemExportQueueProducerContextProvider.php
      - src/BusinessSchema/Application/BusinessSchemaExecutionStateGuard.php
      - src/BusinessSchema/Application/BusinessSchemaExecutor.php
      - src/BusinessSchema/Application/BusinessSchemaLifecycleManager.php
      - src/BusinessSchema/Application/BusinessSchemaPlanRepository.php
      - src/BusinessSchema/Application/BusinessSchemaPlanner.php
      - src/BusinessSchema/Application/BusinessSchemaRecoveryEvidenceRepository.php
      - src/BusinessSchema/Application/BusinessSchemaService.php
      - src/BusinessSchema/Application/DefinitionPhysicalSchemaCompiler.php
      - src/BusinessSchema/Application/PublishedDefinitionSchemaObserver.php
      - src/BusinessSchema/Delivery/Api/BusinessSchemaApiHandler.php
      - src/BusinessSchema/Infrastructure/Execution/DoctrineBusinessSchemaExecutionStateGuard.php
      - src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaPlanRepository.php
      - src/BusinessSchema/Infrastructure/Persistence/DoctrineBusinessSchemaRecoveryEvidenceRepository.php
      - src/BusinessSchema/Infrastructure/Schema/CanonicalDefinitionPhysicalSchemaCompiler.php
      - src/BusinessSecurity/Application/Administration/BusinessSecurityAdministrationService.php
      - src/BusinessSecurity/Application/Approval/ApprovalBinding.php
      - src/BusinessSecurity/Application/Approval/ApprovalQueryRepository.php
      - src/BusinessSecurity/Application/Approval/ApprovalQueryService.php
      - src/BusinessSecurity/Application/Approval/ApprovalRepository.php
      - src/BusinessSecurity/Application/Approval/ApprovalService.php
      - src/BusinessSecurity/Application/Approval/StepUpProofConsumer.php
      - src/BusinessSecurity/Application/BusinessRecordAccessCatalogPlanner.php
      - src/BusinessSecurity/Application/BusinessRecordAccessController.php
      - src/BusinessSecurity/Application/BusinessRecordAccessOperationCatalogPlanner.php
      - src/BusinessSecurity/Application/MembershipDirectory.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineApprovalQueryRepository.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineApprovalRepository.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessController.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessSecurityAdministrationRepository.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineMembershipDirectory.php
      - src/BusinessSecurity/Infrastructure/Persistence/DoctrineStepUpProofConsumer.php
      - src/BusinessSurface/Application/BusinessApprovalExposureCatalog.php
      - src/BusinessSurface/Application/BusinessApprovalSurfaceService.php
      - src/BusinessSurface/Application/BusinessHistoryUseCase.php
      - src/BusinessSurface/Application/BusinessMutationPlanService.php
      - src/BusinessSurface/Application/BusinessOperationStatusRepository.php
      - src/BusinessSurface/Application/BusinessOperationStatusService.php
      - src/BusinessSurface/Application/BusinessSurface.php
      - src/BusinessSurface/Application/BusinessSurfaceCatalog.php
      - src/BusinessSurface/Application/BusinessSurfaceService.php
      - src/BusinessSurface/Application/BusinessSurfaceUseCases.php
      - src/BusinessSurface/Application/Custom/CustomBusinessSurfaceDispatcher.php
      - src/BusinessSurface/Application/CustomBusinessActionExecutor.php
      - src/BusinessSurface/Application/GeneratedBusinessActionStepUp.php
      - src/BusinessSurface/Delivery/Administrator/AdministratorBusinessSurfaceHandler.php
      - src/BusinessSurface/Delivery/Browser/GeneratedBusinessBrowserController.php
      - src/BusinessSurface/Delivery/Portal/PortalBusinessSurfaceHandler.php
      - src/BusinessSurface/Infrastructure/Persistence/DoctrineBusinessOperationStatusRepository.php
      - src/Content/Application/ContentModelRepository.php
      - src/Content/Application/ContentModelService.php
      - src/Content/Application/ContentRecord.php
      - src/Content/Application/ContentSearchRepository.php
      - src/Content/Application/ContentService.php
      - src/Content/Application/SiteScopedContentRepository.php
      - src/Content/Application/TranslationGroupRepository.php
      - src/Content/Domain/ContentTypeDefinition.php
      - src/Content/Infrastructure/Persistence/DoctrineContentModelRepository.php
      - src/Content/Infrastructure/Persistence/DoctrineContentRepository.php
      - src/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepository.php
      - src/Content/Presentation/TranslationGroupPresenter.php
      - src/Delivery/Console/Command/ConsoleAuthorizer.php
      - src/Delivery/Console/Command/CreateAccessTokenCommand.php
      - src/Delivery/Console/Command/CreateAdministratorCommand.php
      - src/Delivery/Console/Command/DemoAccessCommand.php
      - src/Delivery/Console/Command/DemoExamplesCommand.php
      - src/Delivery/Console/Command/DemoExportCommand.php
      - src/Delivery/Console/Command/DemoInstallCommand.php
      - src/Delivery/Console/Command/ManageAccessCommand.php
      - src/Delivery/Console/Command/ManageAutomationCommand.php
      - src/Delivery/Console/Command/ManageBusinessDefinitionsCommand.php
      - src/Delivery/Console/Command/ManageBusinessRecordsCommand.php
      - src/Delivery/Console/Command/ManageBusinessSchemaCommand.php
      - src/Delivery/Console/Command/ManageContentCommand.php
      - src/Delivery/Console/Command/ManageNavigationCommand.php
      - src/Delivery/Console/Command/ManageTrustStoreCommand.php
      - src/Delivery/Console/Command/McpServeCommand.php
      - src/Delivery/Console/Command/MigrateCommand.php
      - src/Delivery/Console/Command/MigrationStatusCommand.php
      - src/Delivery/Console/Command/QueueWorkCommand.php
      - src/Delivery/Console/Command/RecoverCredentialsCommand.php
      - src/Delivery/Console/Command/RecoverMigrationLockCommand.php
      - src/Delivery/Console/Command/ScheduleRunCommand.php
      - src/Delivery/Http/Api/ApiExecutionContext.php
      - src/Delivery/Http/Api/Business/BusinessRecordApiHandler.php
      - src/Delivery/Http/Api/Idempotency/HttpMutationPreauthorizer.php
      - src/Delivery/Http/Api/Idempotency/PersistentIdempotencyMiddleware.php
      - src/Delivery/Http/Api/Idempotency/SecretOnceIdempotencyMiddleware.php
      - src/Delivery/Http/Api/Navigation/NavigationApiRequest.php
      - src/Delivery/Http/Mcp/McpHttpHandler.php
      - src/Demo/Application/DemoBusinessTemplateProjector.php
      - src/Demo/Application/VdmBusinessManifestProjector.php
      - src/Demo/Infrastructure/DemoAccessProvisioner.php
      - src/Demo/Infrastructure/DemoBusinessProfileExporter.php
      - src/Demo/Infrastructure/DemoContentProfileInstaller.php
      - src/Demo/Infrastructure/DemoExampleExtensionInstaller.php
      - src/Demo/Infrastructure/DemoProfileExporter.php
      - src/Demo/Infrastructure/DemoProfileInstaller.php
      - src/Demo/Infrastructure/VdmBusinessDemoInstaller.php
      - src/Extension/Application/ExtensionManager.php
      - src/Extension/Application/Package/ExtensionActivationAdmission.php
      - src/Extension/Application/Trust/RevocationFeedSynchronizer.php
      - src/Extension/Application/Trust/TrustStore.php
      - src/Extension/Infrastructure/DoctrineExtensionManager.php
      - src/Extension/Infrastructure/RedisLockedExtensionManager.php
      - src/Http/Handler/MediaAssetHandler.php
      - src/Http/Handler/StudioPublishedStylesheetHandler.php
      - src/Http/Middleware/BearerAuthenticationMiddleware.php
      - src/Identity/Application/Administration/AccessControlService.php
      - src/Identity/Application/Administration/AdministratorIdentityGateway.php
      - src/Identity/Application/Administration/AdministratorSession.php
      - src/Identity/Application/Administration/AdministratorSessionStore.php
      - src/Identity/Application/Administration/TokenDelegationPreauthorizer.php
      - src/Identity/Application/Administration/TokenRotationPreauthorizer.php
      - src/Identity/Application/Authentication/AuthenticatedPrincipal.php
      - src/Identity/Application/Authentication/VerifiedAccessToken.php
      - src/Identity/Application/StepUp/AuthorizationStepUpProofAdapter.php
      - src/Identity/Infrastructure/Administration/DoctrineAdministratorIdentityGateway.php
      - src/Identity/Infrastructure/Administration/DoctrineAdministratorSessionStore.php
      - src/Identity/Infrastructure/Authentication/DoctrineAccessTokenVerifier.php
      - src/Infrastructure/Authorization/DoctrineResourceSiteOwnership.php
      - src/Infrastructure/Authorization/DoctrineResourceSiteOwnershipWriter.php
      - src/Infrastructure/Authorization/DoctrineSiteGroupWriter.php
      - src/Infrastructure/Automation/DoctrineJobQueue.php
      - src/Infrastructure/Automation/DoctrineQueueRuntimeOperations.php
      - src/Infrastructure/Automation/DoctrineScheduler.php
      - src/Infrastructure/Mcp/BusinessMcpHandlers.php
      - src/Infrastructure/Mcp/KumweMcpHandlers.php
      - src/Infrastructure/Mcp/McpMutationGuard.php
      - src/Infrastructure/Mcp/ReportMcpHandlers.php
      - src/Infrastructure/Persistence/Migration/ApplicationAuthorizationMigration.php
      - src/Infrastructure/Persistence/Migration/ApplicationAuthorizationMigrationRecovery.php
      - src/Infrastructure/Persistence/Migration/BusinessSecurityPortalMigration.php
      - src/Infrastructure/Persistence/Migration/ContentModelRuntimeMigration.php
      - src/Infrastructure/Persistence/Migration/DocumentContentTypesMigration.php
      - src/Infrastructure/Persistence/Migration/DynamicSiteContentMigration.php
      - src/Infrastructure/Persistence/Migration/InterfaceMessageOverrideMigration.php
      - src/Infrastructure/Persistence/Migration/MigrationRunner.php
      - src/Infrastructure/Persistence/Migration/PeriodPostingLockMigration.php
      - src/Infrastructure/Persistence/Migration/ResourceOwnershipScopeMigration.php
      - src/Infrastructure/Persistence/Migration/StudioHostSessionMigration.php
      - src/Infrastructure/Presentation/Persistence/DoctrinePresentationAccessGroupRepository.php
      - src/Infrastructure/Security/DoctrineHighImpactCredentialGuard.php
      - src/Kernel/Configuration/ApplicationConfiguration.php
      - src/Kernel/ContainerFactory.php
      - src/Kernel/DeferredBusinessSchemaObserver.php
      - src/Localization/Application/MessageOverrideService.php
      - src/Localization/Http/Middleware/TranslationScopeMiddleware.php
      - src/Media/Application/BoundedMediaChoiceStorage.php
      - src/Media/Application/MediaService.php
      - src/Media/Application/MediaStorage.php
      - src/Media/Infrastructure/FilesystemMediaStorage.php
      - src/Navigation/Application/NavigationService.php
      - src/Navigation/Application/PublicNavigation.php
      - src/OpenApi/Application/OpenApiComponentClaimAdmission.php
      - src/OpenApi/Application/OpenApiContractProvider.php
      - src/OpenApi/Application/OpenApiContractService.php
      - src/OpenApi/Application/OpenApiExtensionActivationAdmission.php
      - src/Portal/Application/DefaultPortalExecutionContextFactory.php
      - src/Portal/Application/MembershipPortalContextResolver.php
      - src/Portal/Application/MembershipPortalSessionIdentityLoader.php
      - src/Portal/Application/PortalContext.php
      - src/Portal/Application/PortalExecutionContextFactory.php
      - src/Portal/Application/PortalPrincipalLoader.php
      - src/Portal/Http/Handler/PortalApprovalHandler.php
      - src/Portal/Http/Middleware/PortalAuthorizationMiddleware.php
      - src/Portal/Http/Middleware/PortalSessionMiddleware.php
      - src/Portal/Http/PortalRequest.php
      - src/Portal/Infrastructure/Identity/DoctrinePortalPrincipalLoader.php
      - src/Portal/Infrastructure/Session/DoctrinePortalSessionStore.php
      - src/Portal/Presentation/PortalContributionRenderer.php
      - src/Presentation/Application/Dashboard/DashboardComposer.php
      - src/Presentation/Application/Preference/PresentationPreferenceContext.php
      - src/Presentation/Application/ThemeActivationGuard.php
      - src/Presentation/Application/ThemeMutationAuthorizer.php
      - src/Presentation/ContentLayoutCatalog.php
      - src/Presentation/Infrastructure/DoctrineThemeActivationGuard.php
      - src/Presentation/Infrastructure/DoctrineThemeMutationAuthorizer.php
      - src/Presentation/Twig/IsolatedTwigEnvironmentFactory.php
      - src/Site/Application/PublicPageLocator.php
      - src/Site/Application/SiteSettings.php
      - src/Site/Infrastructure/Persistence/CachedSiteSettings.php
      - src/Site/Infrastructure/Persistence/DoctrineSiteSettings.php
      - src/Studio/Application/Authoring/ContentStudioAuthoringContextAuthority.php
      - src/Studio/Application/Authoring/ContentStudioAuthoringContextBinding.php
      - src/Studio/Application/Authoring/ContentStudioAuthoringLaunchResolver.php
      - src/Studio/Application/Authoring/ContentStudioAuthoringTargetResolver.php
      - src/Studio/Application/Authoring/StudioContextualAuthoringConfigurationProvider.php
      - src/Studio/Application/Authoring/UnavailableStudioContextualAuthoringConfigurationProvider.php
      - src/Studio/Application/Composition/CanonicalStudioPublishedContentRenderer.php
      - src/Studio/Application/Composition/StudioContentCompositionService.php
      - src/Studio/Application/Composition/StudioPublishedCompositionGuard.php
      - src/Studio/Application/Composition/StudioPublishedTheme.php
      - src/Studio/Application/Host/StudioArtifactHostPort.php
      - src/Studio/Application/Host/StudioArtifactPublicationGuard.php
      - src/Studio/Application/Host/StudioHostSessionAuthority.php
      - src/Studio/Application/Host/StudioProducerHostFactory.php
      - src/Studio/Application/Host/StudioProducerRequestAuthority.php
      - src/Studio/Application/Host/StudioResourceSearchProvider.php
      - src/Studio/Application/Media/StudioMediaOperations.php
      - src/Studio/Application/Media/StudioMediaService.php
      - src/Studio/Application/Preview/ContentStudioPreviewBindingSource.php
      - src/Studio/Application/Preview/StudioPreviewActivityRecorder.php
      - src/Studio/Application/Preview/StudioPreviewBindingSource.php
      - src/Studio/Application/Preview/StudioPreviewDocumentClaimer.php
      - src/Studio/Application/Preview/StudioPreviewHostPort.php
      - src/Studio/Application/Projection/ContentProjectionBindingRepository.php
      - src/Studio/Application/Projection/ContentStudioProjector.php
      - src/Studio/Application/Projection/ContentStudioResourceSearchProvider.php
      - src/Studio/Application/Projection/RecordAuthorizedStudioContentFieldDisclosure.php
      - src/Studio/Application/Projection/StudioContentFieldDisclosure.php
      - src/Studio/Application/Projection/StudioContentProjectionService.php
      - src/Studio/Domain/Projection/ContentBlueprintBinding.php
      - src/Studio/Domain/Projection/EntryCompositionOverrides.php
      - src/Studio/Infrastructure/Observability/StructuredLogStudioPreviewActivityRecorder.php
      - src/Studio/Infrastructure/Persistence/DoctrineContentProjectionBindingRepository.php
      - src/Studio/Presentation/Preview/CanonicalStudioPreviewRenderer.php
      - src/Workflow/Domain/WorkflowDefinition.php
    configuration_and_di:
      - src/Kernel/ContainerFactory.php
      - tests/Unit/Extension/Runtime/RestrictedExtensionContainerTest.php
    reflection_and_string_references:
      - "docs/migration-consumers.json inventories exact source/config/escaped-string references."
    fixtures_and_examples:
      - tests/Fixtures/Governance/clean/docs/architecture/layers.json
      - tests/Functional/BusinessSurface/GeneratedBusinessAdapterParityTest.php
      - tests/Functional/BusinessSurface/GeneratedBusinessDataEntryRetentionTest.php
      - tests/Functional/BusinessSurface/GeneratedBusinessSurfaceWordingTest.php
      - tests/Integration/Audit/AuditEnforcementUnavailableTest.php
      - tests/Integration/Audit/AuditTamperEvidenceTest.php
      - tests/Integration/Audit/AuditTrailRuntimeIntegrationTest.php
      - tests/Integration/Authorization/BusinessGroupOwnershipEngineIntegrationTest.php
      - tests/Integration/Automation/AutomationManagementIntegrationTest.php
      - tests/Integration/Automation/IdempotencyFirstClaimContentionIntegrationTest.php
      - tests/Integration/Automation/IdempotencyRecoveryIntegrationTest.php
      - tests/Integration/Automation/KilledWorkerRecoveryIntegrationTest.php
      - tests/Integration/BusinessIntegration/PoisonAndDeadLetterIntegrationTest.php
      - tests/Integration/BusinessRecord/AggregateDocumentConcurrencyIntegrationTest.php
      - tests/Integration/BusinessRecord/AggregateDocumentIntegrationTest.php
      - tests/Integration/BusinessRecord/BusinessNumberSequenceContentionIntegrationTest.php
      - tests/Integration/BusinessRecord/BusinessNumberSequenceHotPathIntegrationTest.php
      - tests/Integration/BusinessRecord/BusinessRecordClientReferenceIntegrationTest.php
      - tests/Integration/BusinessRecord/BusinessRecordPolicyCompilerIntegrationTest.php
      - tests/Integration/BusinessRecord/BusinessRecordRelationshipIntegrationTest.php
      - tests/Integration/BusinessRecord/CorrectionAfterPeriodCloseIntegrationTest.php
      - tests/Integration/BusinessRecord/DocumentCommitInstrumentationIntegrationTest.php
      - tests/Integration/BusinessRecord/FiscalPeriodSequenceIntegrationTest.php
      - tests/Integration/BusinessRecord/ImmutableRecordReversalIntegrationTest.php
      - tests/Integration/BusinessRecord/PostingPeriodLockIntegrationTest.php
      - tests/Integration/BusinessRecord/RecordSecretRotationIntegrationTest.php
      - tests/Integration/BusinessReporting/ExportArtifactPersistenceTest.php
      - tests/Integration/BusinessReporting/ExportAttemptPublisherTest.php
      - tests/Integration/BusinessSecurity/DoctrineApprovalQueryRepositoryIntegrationTest.php
      - tests/Integration/BusinessSurface/GeneratedBusinessActionExposureIntegrationTest.php
      - tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php
      - tests/Integration/BusinessSurface/GeneratedBusinessQueryBudgetIntegrationTest.php
      - tests/Integration/BusinessSurface/GeneratedBusinessRelatedPolicyIntegrationTest.php
      - tests/Integration/Content/MultilingualContentIntegrationTest.php
      - tests/Integration/Delivery/Http/Api/Idempotency/SecretOnceIdempotencyMiddlewareTest.php
      - tests/Integration/Demo/Infrastructure/DemoBusinessProfileExportInstallIntegrationTest.php
      - tests/Integration/Extension/ContributedContentTranslationIntegrationTest.php
      - tests/Integration/Extension/ExtensionContributionLifecycleIntegrationTest.php
      - tests/Integration/Extension/ExtensionOwnershipLifecycleIntegrationTest.php
      - tests/Integration/Extension/GeneratedExtensionLifecycleIntegrationTest.php
      - tests/Integration/Extension/ManifestGenerationLifecycleIntegrationTest.php
      - tests/Integration/Extension/SupplyChainAdmissionIntegrationTest.php
      - tests/Integration/Identity/AccessControlIntegrationTest.php
      - tests/Integration/Identity/CredentialLifecycleIntegrationTest.php
      - tests/Integration/Infrastructure/UnwritableStorageIntegrationTest.php
      - tests/Integration/Localization/AdministratorWordingValidationIntegrationTest.php
      - tests/Integration/Localization/MessageOverrideIntegrationTest.php
      - tests/Integration/Mcp/McpProtocolContractIntegrationTest.php
      - tests/Integration/OpenApi/OpenApiContractGenerationIntegrationTest.php
      - tests/Integration/Performance/HotPlanRegressionIntegrationTest.php
      - tests/Integration/Persistence/CrashResumableMigrationIntegrationTest.php
      - tests/Integration/Persistence/MigrationIntegrationTest.php
      - tests/Integration/Presentation/DoctrineThemeManagerIntegrationTest.php
      - tests/Integration/Presentation/McpThemeIntegrationTest.php
      - tests/Integration/Presentation/ThemePersistenceIntegrationTest.php
      - tests/Integration/Studio/ContentStudioAuthoringContextPersistenceTest.php
      - tests/Integration/Studio/ExtensionStudioPreviewRendererIntegrationTest.php
      - tests/Integration/Studio/StudioArtifactRecoveryProducerIntegrationTest.php
      - tests/Integration/Studio/StudioArtifactRecoveryVectorReplayIntegrationTest.php
      - tests/Integration/Studio/StudioContentProjectionPersistenceTest.php
      - tests/Support/AllowingAuditAuthorization.php
      - tests/Support/AssetInspectionDeploymentAcceptance.php
      - tests/Support/AuthorizationContext.php
      - tests/Support/BusinessRuntimeBackupAcceptance.php
      - tests/Support/CapabilityThemeAuthorizer.php
      - tests/Support/DashboardPreferenceTestRuntime.php
      - tests/Support/DrillDirectedJobHandler.php
      - tests/Support/InMemoryPresentationAccessGroupRepository.php
      - tests/Support/NeutralBusinessFixture.php
      - tests/Support/RestoreSecurityAcceptance.php
      - tests/Support/RestoredWork.php
      - tests/Support/StudioProducerRequest.php
      - tests/Support/TestKernelFactory.php
      - tests/Support/TransientBusinessDefinitionFixtureScope.php
      - tests/Support/customize-demo-profile.php
      - tests/Support/prepare-browser-contribution.php
      - tests/Unit/Administrator/Content/ContentFormTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorAccessControlHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorRetentionTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorDashboardHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorDashboardPreferencesHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorExtensionActionHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorLoginHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorMediaHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorStudioHostHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorStudioMediaUploadHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorStudioPreviewDocumentHandlerTest.php
      - tests/Unit/Administrator/Http/Handler/AdministratorStudioSessionHandlerTest.php
      - tests/Unit/Administrator/Http/Middleware/AdministratorAuthorizationMiddlewareTest.php
      - tests/Unit/Administrator/Http/Middleware/AdministratorSessionMiddlewareTest.php
      - tests/Unit/Administrator/Presentation/AdministratorContributionRendererTest.php
      - tests/Unit/Application/Authorization/AdapterAuthorizationParityTest.php
      - tests/Unit/Application/Authorization/ApplicationAuthorizationTest.php
      - tests/Unit/Application/Authorization/BusinessGroupOwnershipTest.php
      - tests/Unit/Application/Authorization/DoctrineResourceSiteOwnershipWriterTest.php
      - tests/Unit/Application/Authorization/OwnershipScopeModelTest.php
      - tests/Unit/Application/Authorization/ResourceOwnershipScopeServiceTest.php
      - tests/Unit/Application/Authorization/SiteGroupAdministrationTest.php
      - tests/Unit/Application/Authorization/SiteScopeContainmentIsNotAWideningTest.php
      - tests/Unit/Application/Automation/AutomationManagementServiceTest.php
      - tests/Unit/Application/Automation/PurgeBusinessRecordIdempotencyHandlerTest.php
      - tests/Unit/Application/Automation/PurgeIdempotencyRecordsHandlerTest.php
      - tests/Unit/Application/Automation/PurgeStudioContentAuthoringContextsHandlerTest.php
      - tests/Unit/Application/Automation/WorkerTest.php
      - tests/Unit/Application/Operations/MigrationLockRecoveryServiceTest.php
      - tests/Unit/Application/Presentation/Dashboard/DashboardPreferenceServiceTest.php
      - tests/Unit/BusinessDefinition/Administrator/BusinessDefinitionFormMapperTest.php
      - tests/Unit/BusinessDefinition/Infrastructure/DoctrineBusinessDefinitionRepositoryTest.php
      - tests/Unit/BusinessIntegration/Application/BusinessRecordMutationEventPublisherTest.php
      - tests/Unit/BusinessIntegration/Application/JobQueueIntegrationEventHandlerTest.php
      - tests/Unit/BusinessIntegration/Application/ValidatedContributedJobHandlerTest.php
      - tests/Unit/BusinessIntegration/ConsumerDispatcherTest.php
      - tests/Unit/BusinessIntegration/ProcessWorkDispatcherTest.php
      - tests/Unit/BusinessRecord/Application/BusinessRecordMutationPublicationTest.php
      - tests/Unit/BusinessRecord/Application/BusinessRecordRelationshipCoordinatorTest.php
      - tests/Unit/BusinessRecord/Application/PostingPeriodLockTest.php
      - tests/Unit/BusinessRecord/Application/PostingPeriodServiceTest.php
      - tests/Unit/BusinessRecord/Application/WriteDocumentCommandTest.php
      - tests/Unit/BusinessRecord/Domain/RecordScopeTest.php
      - tests/Unit/BusinessReporting/ExportArtifactTest.php
      - tests/Unit/BusinessReporting/ExportGenerationPolicyFenceTest.php
      - tests/Unit/BusinessReporting/ExportServiceTransactionTest.php
      - tests/Unit/BusinessReporting/LiveExportExecutionContextResolverTest.php
      - tests/Unit/BusinessReporting/RecordExportPipelineTest.php
      - tests/Unit/BusinessReporting/RecordExportReportProviderTest.php
      - tests/Unit/BusinessReporting/ReportApiDiscoveryTest.php
      - tests/Unit/BusinessReporting/ReportBrowserErrorResponseTest.php
      - tests/Unit/BusinessReporting/ReportDeliveryPresenterTest.php
      - tests/Unit/BusinessReporting/ReportPolicyInferenceTest.php
      - tests/Unit/BusinessSchema/Infrastructure/CanonicalDefinitionPhysicalSchemaCompilerTest.php
      - tests/Unit/BusinessSecurity/Application/ApprovalServiceTest.php
      - tests/Unit/BusinessSecurity/Application/BusinessSecurityAdministrationServiceTest.php
      - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineApprovalRepositoryTest.php
      - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessControllerTest.php
      - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineStepUpProofConsumerTest.php
      - tests/Unit/BusinessSurface/Application/BusinessApprovalSurfaceServiceTest.php
      - tests/Unit/BusinessSurface/Application/BusinessMutationPlanServiceTest.php
      - tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php
      - tests/Unit/BusinessSurface/Application/BusinessSurfaceServiceTest.php
      - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessActionExecutorTest.php
      - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessHandlerRegistryTest.php
      - tests/Unit/BusinessSurface/Application/GeneratedBusinessActionStepUpTest.php
      - tests/Unit/Content/Application/ContentTranslationServiceTest.php
      - tests/Unit/Content/Application/ContributedContentTranslationTest.php
      - tests/Unit/Content/Infrastructure/Persistence/DoctrineContentRepositoryTest.php
      - tests/Unit/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepositoryTest.php
      - tests/Unit/Content/Presentation/TranslationGroupPresenterTest.php
      - tests/Unit/Delivery/Console/Command/AuditConsoleCommandTest.php
      - tests/Unit/Delivery/Console/Command/CreateAccessTokenCommandTest.php
      - tests/Unit/Delivery/Console/Command/QueueWorkCommandTest.php
      - tests/Unit/Delivery/Console/Command/RotateRecordSecretsCommandTest.php
      - tests/Unit/Delivery/Console/Command/ThemeCommandTest.php
      - tests/Unit/Delivery/Http/Api/Automation/AutomationApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Business/BusinessApprovalApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Business/BusinessOperationStatusApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Business/BusinessRecordApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Business/PostingPeriodApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Extension/ExtensionApiHandlerTest.php
      - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyAuthorizationTest.php
      - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyMiddlewareTest.php
      - tests/Unit/Delivery/Http/Mcp/McpHttpHandlerTest.php
      - tests/Unit/Demo/Application/DemoBusinessTemplateProjectorTest.php
      - tests/Unit/Demo/Application/VdmBusinessManifestProjectorTest.php
      - tests/Unit/Demo/Infrastructure/DemoExampleExtensionInstallerTest.php
      - tests/Unit/Demo/Infrastructure/VdmBusinessDefinitionSchemaTest.php
      - tests/Unit/Demo/Infrastructure/VdmBusinessDemoInstallerTest.php
      - tests/Unit/Extension/Runtime/RestrictedExtensionContainerTest.php
      - tests/Unit/Http/Handler/HomePageHandlerTest.php
      - tests/Unit/Http/Handler/PublishedContentHandlerTest.php
      - tests/Unit/Http/Middleware/BearerAuthenticationMiddlewareTest.php
      - tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php
      - tests/Unit/Identity/Application/Administration/TokenDelegationPreauthorizerTest.php
      - tests/Unit/Identity/Application/Authentication/AuthenticatedPrincipalTest.php
      - tests/Unit/Identity/Infrastructure/Administration/DoctrineAdministratorSessionStoreTest.php
      - tests/Unit/Infrastructure/Mcp/BusinessMcpHandlersTest.php
      - tests/Unit/Infrastructure/Persistence/Migration/MigrationRunnerTest.php
      - tests/Unit/Infrastructure/Security/DoctrineHighImpactCredentialGuardTest.php
      - tests/Unit/InterfaceStandard/PresentationPreferenceManagerTest.php
      - tests/Unit/InterfaceStandard/PresentationPreferenceResolverTest.php
      - tests/Unit/Localization/Application/LocaleNegotiationTest.php
      - tests/Unit/Localization/Application/MessageOverrideServiceTest.php
      - tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php
      - tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php
      - tests/Unit/Media/Infrastructure/FilesystemMediaStorageTest.php
      - tests/Unit/Navigation/Application/NavigationServiceTest.php
      - tests/Unit/Navigation/Application/PublicNavigationTest.php
      - tests/Unit/OpenApi/Application/OpenApiComponentClaimAdmissionTest.php
      - tests/Unit/OpenApi/Application/OpenApiExtensionActivationAdmissionTest.php
      - tests/Unit/OpenApi/Delivery/Http/OpenApiHandlerTest.php
      - tests/Unit/Portal/Application/MembershipPortalSessionIdentityLoaderTest.php
      - tests/Unit/Portal/Http/PortalDashboardPreferencesHandlerTest.php
      - tests/Unit/Portal/Http/PortalHomeHandlerTest.php
      - tests/Unit/Portal/Http/PortalSecurityBoundaryTest.php
      - tests/Unit/Portal/Http/PortalSecurityHandlerTest.php
      - tests/Unit/Portal/Infrastructure/Identity/DoctrinePortalPrincipalLoaderTest.php
      - tests/Unit/Portal/Presentation/PortalContributionRendererTest.php
      - tests/Unit/Presentation/ContentLayoutCatalogTest.php
      - tests/Unit/Presentation/Twig/IsolatedTwigEnvironmentFactoryTest.php
      - tests/Unit/Site/Application/PublicPageLocatorTest.php
      - tests/Unit/Site/Infrastructure/Persistence/DoctrineSiteSettingsTest.php
      - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringContextAuthorityTest.php
      - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringTargetResolverTest.php
      - tests/Unit/Studio/Application/Composition/StudioPublishedContentRendererTest.php
      - tests/Unit/Studio/Application/Composition/StudioPublishedThemeTest.php
      - tests/Unit/Studio/Application/Host/StudioArtifactHostPortTest.php
      - tests/Unit/Studio/Application/Host/StudioHostSessionAuthorityTest.php
      - tests/Unit/Studio/Application/Host/StudioProducerMutationBoundaryTest.php
      - tests/Unit/Studio/Application/Host/StudioProducerRequestAuthorityTest.php
      - tests/Unit/Studio/Application/Host/StudioResourceHostPortTest.php
      - tests/Unit/Studio/Application/Media/StudioMediaAuditTest.php
      - tests/Unit/Studio/Application/Media/StudioMediaLifecycleTest.php
      - tests/Unit/Studio/Application/Media/StudioMediaProducerPortTest.php
      - tests/Unit/Studio/Application/Preview/ContentStudioPreviewBindingSourceTest.php
      - tests/Unit/Studio/Application/Preview/StudioPreviewActivityRecorderTest.php
      - tests/Unit/Studio/Application/Preview/StudioPreviewBindingRendererTest.php
      - tests/Unit/Studio/Application/Preview/StudioPreviewHostPortTest.php
      - tests/Unit/Studio/Application/Preview/StudioPreviewProducerPortTest.php
      - tests/Unit/Studio/Application/Projection/ContentStudioProjectorTest.php
      - tests/Unit/Studio/Application/Projection/ContentStudioResourceSearchProviderTest.php
      - tests/Unit/Studio/Application/Projection/RecordAuthorizedStudioContentFieldDisclosureTest.php
      - tests/Unit/Studio/Application/Projection/StudioContentProjectionServiceTest.php
      - tests/Unit/Studio/Domain/Projection/ContentBlueprintBindingTest.php
      - tests/Unit/Studio/Domain/Projection/EntryCompositionOverridesTest.php
      - tests/Unit/Studio/Infrastructure/Persistence/DoctrineContentProjectionBindingRepositoryTest.php
      - tests/Unit/Support/TransientBusinessDefinitionFixtureScopeTest.php
      - tests/Unit/Workflow/Domain/WorkflowTest.php
    external: []
  dependency_injection:
    mode: direct
    provider: null
    factories: []
    aliases: []
    service_lifetimes:
      - "Operation supplied; never register current actor/site/membership as a shared service."
    configuration_keys: []
    provider_absence_reason: "Only values, enums, contracts and an exception; host adapters supply context explicitly."
native_cpp: null
php_extension: null
tests:
  moved_or_added:
    - tests/Case/ArchitectureTest.php
    - tests/Case/AuthenticatedSurfaceTest.php
    - tests/Case/AuthenticationStrengthTest.php
    - tests/Case/ExecutionContextTest.php
    - tests/Case/InvalidContextTest.php
    - tests/Case/MembershipContextTest.php
    - tests/Case/OrganizationContextTest.php
    - tests/Case/PublicManifestsTest.php
    - tests/Case/SiteContextTest.php
    - tests/Case/StepUpProofTest.php
    - tests/Case/WorkspaceContextTest.php
  remain_in_app_or_consumer:
    - tests/Fixtures/Governance/clean/docs/architecture/layers.json
    - tests/Functional/BusinessSurface/GeneratedBusinessAdapterParityTest.php
    - tests/Functional/BusinessSurface/GeneratedBusinessDataEntryRetentionTest.php
    - tests/Functional/BusinessSurface/GeneratedBusinessSurfaceWordingTest.php
    - tests/Integration/Audit/AuditEnforcementUnavailableTest.php
    - tests/Integration/Audit/AuditTamperEvidenceTest.php
    - tests/Integration/Audit/AuditTrailRuntimeIntegrationTest.php
    - tests/Integration/Authorization/BusinessGroupOwnershipEngineIntegrationTest.php
    - tests/Integration/Automation/AutomationManagementIntegrationTest.php
    - tests/Integration/Automation/IdempotencyFirstClaimContentionIntegrationTest.php
    - tests/Integration/Automation/IdempotencyRecoveryIntegrationTest.php
    - tests/Integration/Automation/KilledWorkerRecoveryIntegrationTest.php
    - tests/Integration/BusinessIntegration/PoisonAndDeadLetterIntegrationTest.php
    - tests/Integration/BusinessRecord/AggregateDocumentConcurrencyIntegrationTest.php
    - tests/Integration/BusinessRecord/AggregateDocumentIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessNumberSequenceContentionIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessNumberSequenceHotPathIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordClientReferenceIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordPolicyCompilerIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordRelationshipIntegrationTest.php
    - tests/Integration/BusinessRecord/CorrectionAfterPeriodCloseIntegrationTest.php
    - tests/Integration/BusinessRecord/DocumentCommitInstrumentationIntegrationTest.php
    - tests/Integration/BusinessRecord/FiscalPeriodSequenceIntegrationTest.php
    - tests/Integration/BusinessRecord/ImmutableRecordReversalIntegrationTest.php
    - tests/Integration/BusinessRecord/PostingPeriodLockIntegrationTest.php
    - tests/Integration/BusinessRecord/RecordSecretRotationIntegrationTest.php
    - tests/Integration/BusinessReporting/ExportArtifactPersistenceTest.php
    - tests/Integration/BusinessReporting/ExportAttemptPublisherTest.php
    - tests/Integration/BusinessSecurity/DoctrineApprovalQueryRepositoryIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessActionExposureIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessQueryBudgetIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessRelatedPolicyIntegrationTest.php
    - tests/Integration/Content/MultilingualContentIntegrationTest.php
    - tests/Integration/Delivery/Http/Api/Idempotency/SecretOnceIdempotencyMiddlewareTest.php
    - tests/Integration/Demo/Infrastructure/DemoBusinessProfileExportInstallIntegrationTest.php
    - tests/Integration/Extension/ContributedContentTranslationIntegrationTest.php
    - tests/Integration/Extension/ExtensionContributionLifecycleIntegrationTest.php
    - tests/Integration/Extension/ExtensionOwnershipLifecycleIntegrationTest.php
    - tests/Integration/Extension/GeneratedExtensionLifecycleIntegrationTest.php
    - tests/Integration/Extension/ManifestGenerationLifecycleIntegrationTest.php
    - tests/Integration/Extension/SupplyChainAdmissionIntegrationTest.php
    - tests/Integration/Identity/AccessControlIntegrationTest.php
    - tests/Integration/Identity/CredentialLifecycleIntegrationTest.php
    - tests/Integration/Infrastructure/UnwritableStorageIntegrationTest.php
    - tests/Integration/Localization/AdministratorWordingValidationIntegrationTest.php
    - tests/Integration/Localization/MessageOverrideIntegrationTest.php
    - tests/Integration/Mcp/McpProtocolContractIntegrationTest.php
    - tests/Integration/OpenApi/OpenApiContractGenerationIntegrationTest.php
    - tests/Integration/Performance/HotPlanRegressionIntegrationTest.php
    - tests/Integration/Persistence/CrashResumableMigrationIntegrationTest.php
    - tests/Integration/Persistence/MigrationIntegrationTest.php
    - tests/Integration/Presentation/DoctrineThemeManagerIntegrationTest.php
    - tests/Integration/Presentation/McpThemeIntegrationTest.php
    - tests/Integration/Presentation/ThemePersistenceIntegrationTest.php
    - tests/Integration/Studio/ContentStudioAuthoringContextPersistenceTest.php
    - tests/Integration/Studio/ExtensionStudioPreviewRendererIntegrationTest.php
    - tests/Integration/Studio/StudioArtifactRecoveryProducerIntegrationTest.php
    - tests/Integration/Studio/StudioArtifactRecoveryVectorReplayIntegrationTest.php
    - tests/Integration/Studio/StudioContentProjectionPersistenceTest.php
    - tests/Support/AllowingAuditAuthorization.php
    - tests/Support/AssetInspectionDeploymentAcceptance.php
    - tests/Support/AuthorizationContext.php
    - tests/Support/BusinessRuntimeBackupAcceptance.php
    - tests/Support/CapabilityThemeAuthorizer.php
    - tests/Support/DashboardPreferenceTestRuntime.php
    - tests/Support/DrillDirectedJobHandler.php
    - tests/Support/InMemoryPresentationAccessGroupRepository.php
    - tests/Support/NeutralBusinessFixture.php
    - tests/Support/RestoreSecurityAcceptance.php
    - tests/Support/RestoredWork.php
    - tests/Support/StudioProducerRequest.php
    - tests/Support/TestKernelFactory.php
    - tests/Support/TransientBusinessDefinitionFixtureScope.php
    - tests/Support/customize-demo-profile.php
    - tests/Support/prepare-browser-contribution.php
    - tests/Unit/Administrator/Content/ContentFormTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorAccessControlHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorRetentionTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorDashboardHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorDashboardPreferencesHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorExtensionActionHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorLoginHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorMediaHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioHostHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioMediaUploadHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioPreviewDocumentHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioSessionHandlerTest.php
    - tests/Unit/Administrator/Http/Middleware/AdministratorAuthorizationMiddlewareTest.php
    - tests/Unit/Administrator/Http/Middleware/AdministratorSessionMiddlewareTest.php
    - tests/Unit/Administrator/Presentation/AdministratorContributionRendererTest.php
    - tests/Unit/Application/Authorization/AdapterAuthorizationParityTest.php
    - tests/Unit/Application/Authorization/ApplicationAuthorizationTest.php
    - tests/Unit/Application/Authorization/BusinessGroupOwnershipTest.php
    - tests/Unit/Application/Authorization/DoctrineResourceSiteOwnershipWriterTest.php
    - tests/Unit/Application/Authorization/OwnershipScopeModelTest.php
    - tests/Unit/Application/Authorization/ResourceOwnershipScopeServiceTest.php
    - tests/Unit/Application/Authorization/SiteGroupAdministrationTest.php
    - tests/Unit/Application/Authorization/SiteScopeContainmentIsNotAWideningTest.php
    - tests/Unit/Application/Automation/AutomationManagementServiceTest.php
    - tests/Unit/Application/Automation/PurgeBusinessRecordIdempotencyHandlerTest.php
    - tests/Unit/Application/Automation/PurgeIdempotencyRecordsHandlerTest.php
    - tests/Unit/Application/Automation/PurgeStudioContentAuthoringContextsHandlerTest.php
    - tests/Unit/Application/Automation/WorkerTest.php
    - tests/Unit/Application/Operations/MigrationLockRecoveryServiceTest.php
    - tests/Unit/Application/Presentation/Dashboard/DashboardPreferenceServiceTest.php
    - tests/Unit/BusinessDefinition/Administrator/BusinessDefinitionFormMapperTest.php
    - tests/Unit/BusinessDefinition/Infrastructure/DoctrineBusinessDefinitionRepositoryTest.php
    - tests/Unit/BusinessIntegration/Application/BusinessRecordMutationEventPublisherTest.php
    - tests/Unit/BusinessIntegration/Application/JobQueueIntegrationEventHandlerTest.php
    - tests/Unit/BusinessIntegration/Application/ValidatedContributedJobHandlerTest.php
    - tests/Unit/BusinessIntegration/ConsumerDispatcherTest.php
    - tests/Unit/BusinessIntegration/ProcessWorkDispatcherTest.php
    - tests/Unit/BusinessRecord/Application/BusinessRecordMutationPublicationTest.php
    - tests/Unit/BusinessRecord/Application/BusinessRecordRelationshipCoordinatorTest.php
    - tests/Unit/BusinessRecord/Application/PostingPeriodLockTest.php
    - tests/Unit/BusinessRecord/Application/PostingPeriodServiceTest.php
    - tests/Unit/BusinessRecord/Application/WriteDocumentCommandTest.php
    - tests/Unit/BusinessRecord/Domain/RecordScopeTest.php
    - tests/Unit/BusinessReporting/ExportArtifactTest.php
    - tests/Unit/BusinessReporting/ExportGenerationPolicyFenceTest.php
    - tests/Unit/BusinessReporting/ExportServiceTransactionTest.php
    - tests/Unit/BusinessReporting/LiveExportExecutionContextResolverTest.php
    - tests/Unit/BusinessReporting/RecordExportPipelineTest.php
    - tests/Unit/BusinessReporting/RecordExportReportProviderTest.php
    - tests/Unit/BusinessReporting/ReportApiDiscoveryTest.php
    - tests/Unit/BusinessReporting/ReportBrowserErrorResponseTest.php
    - tests/Unit/BusinessReporting/ReportDeliveryPresenterTest.php
    - tests/Unit/BusinessReporting/ReportPolicyInferenceTest.php
    - tests/Unit/BusinessSchema/Infrastructure/CanonicalDefinitionPhysicalSchemaCompilerTest.php
    - tests/Unit/BusinessSecurity/Application/ApprovalServiceTest.php
    - tests/Unit/BusinessSecurity/Application/BusinessSecurityAdministrationServiceTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineApprovalRepositoryTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessControllerTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineStepUpProofConsumerTest.php
    - tests/Unit/BusinessSurface/Application/BusinessApprovalSurfaceServiceTest.php
    - tests/Unit/BusinessSurface/Application/BusinessMutationPlanServiceTest.php
    - tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php
    - tests/Unit/BusinessSurface/Application/BusinessSurfaceServiceTest.php
    - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessActionExecutorTest.php
    - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessHandlerRegistryTest.php
    - tests/Unit/BusinessSurface/Application/GeneratedBusinessActionStepUpTest.php
    - tests/Unit/Content/Application/ContentTranslationServiceTest.php
    - tests/Unit/Content/Application/ContributedContentTranslationTest.php
    - tests/Unit/Content/Infrastructure/Persistence/DoctrineContentRepositoryTest.php
    - tests/Unit/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepositoryTest.php
    - tests/Unit/Content/Presentation/TranslationGroupPresenterTest.php
    - tests/Unit/Delivery/Console/Command/AuditConsoleCommandTest.php
    - tests/Unit/Delivery/Console/Command/CreateAccessTokenCommandTest.php
    - tests/Unit/Delivery/Console/Command/QueueWorkCommandTest.php
    - tests/Unit/Delivery/Console/Command/RotateRecordSecretsCommandTest.php
    - tests/Unit/Delivery/Console/Command/ThemeCommandTest.php
    - tests/Unit/Delivery/Http/Api/Automation/AutomationApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessApprovalApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessOperationStatusApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessRecordApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/PostingPeriodApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Extension/ExtensionApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyAuthorizationTest.php
    - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyMiddlewareTest.php
    - tests/Unit/Delivery/Http/Mcp/McpHttpHandlerTest.php
    - tests/Unit/Demo/Application/DemoBusinessTemplateProjectorTest.php
    - tests/Unit/Demo/Application/VdmBusinessManifestProjectorTest.php
    - tests/Unit/Demo/Infrastructure/DemoExampleExtensionInstallerTest.php
    - tests/Unit/Demo/Infrastructure/VdmBusinessDefinitionSchemaTest.php
    - tests/Unit/Demo/Infrastructure/VdmBusinessDemoInstallerTest.php
    - tests/Unit/Extension/Runtime/RestrictedExtensionContainerTest.php
    - tests/Unit/Http/Handler/HomePageHandlerTest.php
    - tests/Unit/Http/Handler/PublishedContentHandlerTest.php
    - tests/Unit/Http/Middleware/BearerAuthenticationMiddlewareTest.php
    - tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php
    - tests/Unit/Identity/Application/Administration/TokenDelegationPreauthorizerTest.php
    - tests/Unit/Identity/Application/Authentication/AuthenticatedPrincipalTest.php
    - tests/Unit/Identity/Infrastructure/Administration/DoctrineAdministratorSessionStoreTest.php
    - tests/Unit/Infrastructure/Mcp/BusinessMcpHandlersTest.php
    - tests/Unit/Infrastructure/Persistence/Migration/MigrationRunnerTest.php
    - tests/Unit/Infrastructure/Security/DoctrineHighImpactCredentialGuardTest.php
    - tests/Unit/InterfaceStandard/PresentationPreferenceManagerTest.php
    - tests/Unit/InterfaceStandard/PresentationPreferenceResolverTest.php
    - tests/Unit/Localization/Application/LocaleNegotiationTest.php
    - tests/Unit/Localization/Application/MessageOverrideServiceTest.php
    - tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php
    - tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php
    - tests/Unit/Media/Infrastructure/FilesystemMediaStorageTest.php
    - tests/Unit/Navigation/Application/NavigationServiceTest.php
    - tests/Unit/Navigation/Application/PublicNavigationTest.php
    - tests/Unit/OpenApi/Application/OpenApiComponentClaimAdmissionTest.php
    - tests/Unit/OpenApi/Application/OpenApiExtensionActivationAdmissionTest.php
    - tests/Unit/OpenApi/Delivery/Http/OpenApiHandlerTest.php
    - tests/Unit/Portal/Application/MembershipPortalSessionIdentityLoaderTest.php
    - tests/Unit/Portal/Http/PortalDashboardPreferencesHandlerTest.php
    - tests/Unit/Portal/Http/PortalHomeHandlerTest.php
    - tests/Unit/Portal/Http/PortalSecurityBoundaryTest.php
    - tests/Unit/Portal/Http/PortalSecurityHandlerTest.php
    - tests/Unit/Portal/Infrastructure/Identity/DoctrinePortalPrincipalLoaderTest.php
    - tests/Unit/Portal/Presentation/PortalContributionRendererTest.php
    - tests/Unit/Presentation/ContentLayoutCatalogTest.php
    - tests/Unit/Presentation/Twig/IsolatedTwigEnvironmentFactoryTest.php
    - tests/Unit/Site/Application/PublicPageLocatorTest.php
    - tests/Unit/Site/Infrastructure/Persistence/DoctrineSiteSettingsTest.php
    - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringContextAuthorityTest.php
    - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringTargetResolverTest.php
    - tests/Unit/Studio/Application/Composition/StudioPublishedContentRendererTest.php
    - tests/Unit/Studio/Application/Composition/StudioPublishedThemeTest.php
    - tests/Unit/Studio/Application/Host/StudioArtifactHostPortTest.php
    - tests/Unit/Studio/Application/Host/StudioHostSessionAuthorityTest.php
    - tests/Unit/Studio/Application/Host/StudioProducerMutationBoundaryTest.php
    - tests/Unit/Studio/Application/Host/StudioProducerRequestAuthorityTest.php
    - tests/Unit/Studio/Application/Host/StudioResourceHostPortTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaAuditTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaLifecycleTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaProducerPortTest.php
    - tests/Unit/Studio/Application/Preview/ContentStudioPreviewBindingSourceTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewActivityRecorderTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewBindingRendererTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewHostPortTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewProducerPortTest.php
    - tests/Unit/Studio/Application/Projection/ContentStudioProjectorTest.php
    - tests/Unit/Studio/Application/Projection/ContentStudioResourceSearchProviderTest.php
    - tests/Unit/Studio/Application/Projection/RecordAuthorizedStudioContentFieldDisclosureTest.php
    - tests/Unit/Studio/Application/Projection/StudioContentProjectionServiceTest.php
    - tests/Unit/Studio/Domain/Projection/ContentBlueprintBindingTest.php
    - tests/Unit/Studio/Domain/Projection/EntryCompositionOverridesTest.php
    - tests/Unit/Studio/Infrastructure/Persistence/DoctrineContentProjectionBindingRepositoryTest.php
    - tests/Unit/Support/TransientBusinessDefinitionFixtureScopeTest.php
    - tests/Unit/Workflow/Domain/WorkflowTest.php
  split_tests:
    - "Mixed context/security tests retain host provenance, authentication and authorization assertions in App."
  prohibited_duplicates:
    - "App tests must not cover vendor implementation classes as their unit under test."
  corpora:
    - "Existing context fingerprint/freshness/hostile-input vectors in tests/Case."
documentation:
  charter: CHARTER.md
  readme: README.md
  public_api: docs/public-api.md
  architecture: docs/architecture.md
  integration_or_consumer: docs/integration.md
  examples:
    - examples/human-context.php
    - examples/system-context.php
    - examples/step-up-context.php
  changelog_record: "CHANGELOG.md ## 0.1.2"
release_expectations:
  version_policy: "SemVer; 0.1.1 successor under D-GOV-6; fresh verification required after publication."
  expected_artifact_types:
    - "Composer library source archive"
  required_checks:
    - "Protected main before publication; exact stable published release reports immutable true."
    - "composer check"
    - "PHP 8.5 CI and exact archive consumer"
    - "Independent source/tag/registry/artifact verification"
  required_registry_or_installer: "Composer / Packagist after maintainer first submission"
  required_external_attestation: true
next_task:
  phase_name: "Independent release verification, then App Phase 2 adoption."
  permitted_only_when:
    - "The release-integrity successor is published from protected main and independently verified."
    - "Maintainer merges this PR and immutable release-on-record completes."
    - "Fresh independent verifier provides a passing external RELEASE-ATTESTATION.yaml."
    - "App governance bootstrap remains merged and the extracted source drift check passes."
  consumer_repository: kumwe/app
  dependency_or_native_change: "Require the exact verified pre-1.0 kumwe/access-context release through Composer."
  namespace_or_api_replacements:
    - "Replace the eight mapped App value types with Kumwe\\Context\\Value types."
    - "Implement Principal and SystemActor using retained App identities; add no historical alias."
  files_to_update:
    - composer.json
    - composer.lock
    - src/Kernel/ContainerFactory.php
    - docs/architecture/migrations/
    - "docs/migration-consumers.json lists exact App consumers for the baseline."
  files_to_remove:
    - src/Application/Authorization/AuthenticatedSurface.php
    - src/Application/Authorization/AuthenticationStrength.php
    - src/Application/Authorization/ExecutionContext.php
    - src/Application/Authorization/MembershipContext.php
    - src/Application/Authorization/OrganizationContext.php
    - src/Application/Authorization/SiteContext.php
    - src/Application/Authorization/StepUpProof.php
    - src/Application/Authorization/WorkspaceContext.php
  tests_to_remove: []
  tests_to_retain_or_add:
    - tests/Fixtures/Governance/clean/docs/architecture/layers.json
    - tests/Functional/BusinessSurface/GeneratedBusinessAdapterParityTest.php
    - tests/Functional/BusinessSurface/GeneratedBusinessDataEntryRetentionTest.php
    - tests/Functional/BusinessSurface/GeneratedBusinessSurfaceWordingTest.php
    - tests/Integration/Audit/AuditEnforcementUnavailableTest.php
    - tests/Integration/Audit/AuditTamperEvidenceTest.php
    - tests/Integration/Audit/AuditTrailRuntimeIntegrationTest.php
    - tests/Integration/Authorization/BusinessGroupOwnershipEngineIntegrationTest.php
    - tests/Integration/Automation/AutomationManagementIntegrationTest.php
    - tests/Integration/Automation/IdempotencyFirstClaimContentionIntegrationTest.php
    - tests/Integration/Automation/IdempotencyRecoveryIntegrationTest.php
    - tests/Integration/Automation/KilledWorkerRecoveryIntegrationTest.php
    - tests/Integration/BusinessIntegration/PoisonAndDeadLetterIntegrationTest.php
    - tests/Integration/BusinessRecord/AggregateDocumentConcurrencyIntegrationTest.php
    - tests/Integration/BusinessRecord/AggregateDocumentIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessNumberSequenceContentionIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessNumberSequenceHotPathIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordClientReferenceIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordPolicyCompilerIntegrationTest.php
    - tests/Integration/BusinessRecord/BusinessRecordRelationshipIntegrationTest.php
    - tests/Integration/BusinessRecord/CorrectionAfterPeriodCloseIntegrationTest.php
    - tests/Integration/BusinessRecord/DocumentCommitInstrumentationIntegrationTest.php
    - tests/Integration/BusinessRecord/FiscalPeriodSequenceIntegrationTest.php
    - tests/Integration/BusinessRecord/ImmutableRecordReversalIntegrationTest.php
    - tests/Integration/BusinessRecord/PostingPeriodLockIntegrationTest.php
    - tests/Integration/BusinessRecord/RecordSecretRotationIntegrationTest.php
    - tests/Integration/BusinessReporting/ExportArtifactPersistenceTest.php
    - tests/Integration/BusinessReporting/ExportAttemptPublisherTest.php
    - tests/Integration/BusinessSecurity/DoctrineApprovalQueryRepositoryIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessActionExposureIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessQueryBudgetIntegrationTest.php
    - tests/Integration/BusinessSurface/GeneratedBusinessRelatedPolicyIntegrationTest.php
    - tests/Integration/Content/MultilingualContentIntegrationTest.php
    - tests/Integration/Delivery/Http/Api/Idempotency/SecretOnceIdempotencyMiddlewareTest.php
    - tests/Integration/Demo/Infrastructure/DemoBusinessProfileExportInstallIntegrationTest.php
    - tests/Integration/Extension/ContributedContentTranslationIntegrationTest.php
    - tests/Integration/Extension/ExtensionContributionLifecycleIntegrationTest.php
    - tests/Integration/Extension/ExtensionOwnershipLifecycleIntegrationTest.php
    - tests/Integration/Extension/GeneratedExtensionLifecycleIntegrationTest.php
    - tests/Integration/Extension/ManifestGenerationLifecycleIntegrationTest.php
    - tests/Integration/Extension/SupplyChainAdmissionIntegrationTest.php
    - tests/Integration/Identity/AccessControlIntegrationTest.php
    - tests/Integration/Identity/CredentialLifecycleIntegrationTest.php
    - tests/Integration/Infrastructure/UnwritableStorageIntegrationTest.php
    - tests/Integration/Localization/AdministratorWordingValidationIntegrationTest.php
    - tests/Integration/Localization/MessageOverrideIntegrationTest.php
    - tests/Integration/Mcp/McpProtocolContractIntegrationTest.php
    - tests/Integration/OpenApi/OpenApiContractGenerationIntegrationTest.php
    - tests/Integration/Performance/HotPlanRegressionIntegrationTest.php
    - tests/Integration/Persistence/CrashResumableMigrationIntegrationTest.php
    - tests/Integration/Persistence/MigrationIntegrationTest.php
    - tests/Integration/Presentation/DoctrineThemeManagerIntegrationTest.php
    - tests/Integration/Presentation/McpThemeIntegrationTest.php
    - tests/Integration/Presentation/ThemePersistenceIntegrationTest.php
    - tests/Integration/Studio/ContentStudioAuthoringContextPersistenceTest.php
    - tests/Integration/Studio/ExtensionStudioPreviewRendererIntegrationTest.php
    - tests/Integration/Studio/StudioArtifactRecoveryProducerIntegrationTest.php
    - tests/Integration/Studio/StudioArtifactRecoveryVectorReplayIntegrationTest.php
    - tests/Integration/Studio/StudioContentProjectionPersistenceTest.php
    - tests/Support/AllowingAuditAuthorization.php
    - tests/Support/AssetInspectionDeploymentAcceptance.php
    - tests/Support/AuthorizationContext.php
    - tests/Support/BusinessRuntimeBackupAcceptance.php
    - tests/Support/CapabilityThemeAuthorizer.php
    - tests/Support/DashboardPreferenceTestRuntime.php
    - tests/Support/DrillDirectedJobHandler.php
    - tests/Support/InMemoryPresentationAccessGroupRepository.php
    - tests/Support/NeutralBusinessFixture.php
    - tests/Support/RestoreSecurityAcceptance.php
    - tests/Support/RestoredWork.php
    - tests/Support/StudioProducerRequest.php
    - tests/Support/TestKernelFactory.php
    - tests/Support/TransientBusinessDefinitionFixtureScope.php
    - tests/Support/customize-demo-profile.php
    - tests/Support/prepare-browser-contribution.php
    - tests/Unit/Administrator/Content/ContentFormTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorAccessControlHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorContentEditorRetentionTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorDashboardHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorDashboardPreferencesHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorExtensionActionHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorLoginHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorMediaHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioHostHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioMediaUploadHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioPreviewDocumentHandlerTest.php
    - tests/Unit/Administrator/Http/Handler/AdministratorStudioSessionHandlerTest.php
    - tests/Unit/Administrator/Http/Middleware/AdministratorAuthorizationMiddlewareTest.php
    - tests/Unit/Administrator/Http/Middleware/AdministratorSessionMiddlewareTest.php
    - tests/Unit/Administrator/Presentation/AdministratorContributionRendererTest.php
    - tests/Unit/Application/Authorization/AdapterAuthorizationParityTest.php
    - tests/Unit/Application/Authorization/ApplicationAuthorizationTest.php
    - tests/Unit/Application/Authorization/BusinessGroupOwnershipTest.php
    - tests/Unit/Application/Authorization/DoctrineResourceSiteOwnershipWriterTest.php
    - tests/Unit/Application/Authorization/OwnershipScopeModelTest.php
    - tests/Unit/Application/Authorization/ResourceOwnershipScopeServiceTest.php
    - tests/Unit/Application/Authorization/SiteGroupAdministrationTest.php
    - tests/Unit/Application/Authorization/SiteScopeContainmentIsNotAWideningTest.php
    - tests/Unit/Application/Automation/AutomationManagementServiceTest.php
    - tests/Unit/Application/Automation/PurgeBusinessRecordIdempotencyHandlerTest.php
    - tests/Unit/Application/Automation/PurgeIdempotencyRecordsHandlerTest.php
    - tests/Unit/Application/Automation/PurgeStudioContentAuthoringContextsHandlerTest.php
    - tests/Unit/Application/Automation/WorkerTest.php
    - tests/Unit/Application/Operations/MigrationLockRecoveryServiceTest.php
    - tests/Unit/Application/Presentation/Dashboard/DashboardPreferenceServiceTest.php
    - tests/Unit/BusinessDefinition/Administrator/BusinessDefinitionFormMapperTest.php
    - tests/Unit/BusinessDefinition/Infrastructure/DoctrineBusinessDefinitionRepositoryTest.php
    - tests/Unit/BusinessIntegration/Application/BusinessRecordMutationEventPublisherTest.php
    - tests/Unit/BusinessIntegration/Application/JobQueueIntegrationEventHandlerTest.php
    - tests/Unit/BusinessIntegration/Application/ValidatedContributedJobHandlerTest.php
    - tests/Unit/BusinessIntegration/ConsumerDispatcherTest.php
    - tests/Unit/BusinessIntegration/ProcessWorkDispatcherTest.php
    - tests/Unit/BusinessRecord/Application/BusinessRecordMutationPublicationTest.php
    - tests/Unit/BusinessRecord/Application/BusinessRecordRelationshipCoordinatorTest.php
    - tests/Unit/BusinessRecord/Application/PostingPeriodLockTest.php
    - tests/Unit/BusinessRecord/Application/PostingPeriodServiceTest.php
    - tests/Unit/BusinessRecord/Application/WriteDocumentCommandTest.php
    - tests/Unit/BusinessRecord/Domain/RecordScopeTest.php
    - tests/Unit/BusinessReporting/ExportArtifactTest.php
    - tests/Unit/BusinessReporting/ExportGenerationPolicyFenceTest.php
    - tests/Unit/BusinessReporting/ExportServiceTransactionTest.php
    - tests/Unit/BusinessReporting/LiveExportExecutionContextResolverTest.php
    - tests/Unit/BusinessReporting/RecordExportPipelineTest.php
    - tests/Unit/BusinessReporting/RecordExportReportProviderTest.php
    - tests/Unit/BusinessReporting/ReportApiDiscoveryTest.php
    - tests/Unit/BusinessReporting/ReportBrowserErrorResponseTest.php
    - tests/Unit/BusinessReporting/ReportDeliveryPresenterTest.php
    - tests/Unit/BusinessReporting/ReportPolicyInferenceTest.php
    - tests/Unit/BusinessSchema/Infrastructure/CanonicalDefinitionPhysicalSchemaCompilerTest.php
    - tests/Unit/BusinessSecurity/Application/ApprovalServiceTest.php
    - tests/Unit/BusinessSecurity/Application/BusinessSecurityAdministrationServiceTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineApprovalRepositoryTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineBusinessRecordAccessControllerTest.php
    - tests/Unit/BusinessSecurity/Infrastructure/Persistence/DoctrineStepUpProofConsumerTest.php
    - tests/Unit/BusinessSurface/Application/BusinessApprovalSurfaceServiceTest.php
    - tests/Unit/BusinessSurface/Application/BusinessMutationPlanServiceTest.php
    - tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php
    - tests/Unit/BusinessSurface/Application/BusinessSurfaceServiceTest.php
    - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessActionExecutorTest.php
    - tests/Unit/BusinessSurface/Application/Custom/CustomBusinessHandlerRegistryTest.php
    - tests/Unit/BusinessSurface/Application/GeneratedBusinessActionStepUpTest.php
    - tests/Unit/Content/Application/ContentTranslationServiceTest.php
    - tests/Unit/Content/Application/ContributedContentTranslationTest.php
    - tests/Unit/Content/Infrastructure/Persistence/DoctrineContentRepositoryTest.php
    - tests/Unit/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepositoryTest.php
    - tests/Unit/Content/Presentation/TranslationGroupPresenterTest.php
    - tests/Unit/Delivery/Console/Command/AuditConsoleCommandTest.php
    - tests/Unit/Delivery/Console/Command/CreateAccessTokenCommandTest.php
    - tests/Unit/Delivery/Console/Command/QueueWorkCommandTest.php
    - tests/Unit/Delivery/Console/Command/RotateRecordSecretsCommandTest.php
    - tests/Unit/Delivery/Console/Command/ThemeCommandTest.php
    - tests/Unit/Delivery/Http/Api/Automation/AutomationApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessApprovalApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessOperationStatusApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/BusinessRecordApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Business/PostingPeriodApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Extension/ExtensionApiHandlerTest.php
    - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyAuthorizationTest.php
    - tests/Unit/Delivery/Http/Api/Idempotency/PersistentIdempotencyMiddlewareTest.php
    - tests/Unit/Delivery/Http/Mcp/McpHttpHandlerTest.php
    - tests/Unit/Demo/Application/DemoBusinessTemplateProjectorTest.php
    - tests/Unit/Demo/Application/VdmBusinessManifestProjectorTest.php
    - tests/Unit/Demo/Infrastructure/DemoExampleExtensionInstallerTest.php
    - tests/Unit/Demo/Infrastructure/VdmBusinessDefinitionSchemaTest.php
    - tests/Unit/Demo/Infrastructure/VdmBusinessDemoInstallerTest.php
    - tests/Unit/Extension/Runtime/RestrictedExtensionContainerTest.php
    - tests/Unit/Http/Handler/HomePageHandlerTest.php
    - tests/Unit/Http/Handler/PublishedContentHandlerTest.php
    - tests/Unit/Http/Middleware/BearerAuthenticationMiddlewareTest.php
    - tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php
    - tests/Unit/Identity/Application/Administration/TokenDelegationPreauthorizerTest.php
    - tests/Unit/Identity/Application/Authentication/AuthenticatedPrincipalTest.php
    - tests/Unit/Identity/Infrastructure/Administration/DoctrineAdministratorSessionStoreTest.php
    - tests/Unit/Infrastructure/Mcp/BusinessMcpHandlersTest.php
    - tests/Unit/Infrastructure/Persistence/Migration/MigrationRunnerTest.php
    - tests/Unit/Infrastructure/Security/DoctrineHighImpactCredentialGuardTest.php
    - tests/Unit/InterfaceStandard/PresentationPreferenceManagerTest.php
    - tests/Unit/InterfaceStandard/PresentationPreferenceResolverTest.php
    - tests/Unit/Localization/Application/LocaleNegotiationTest.php
    - tests/Unit/Localization/Application/MessageOverrideServiceTest.php
    - tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php
    - tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php
    - tests/Unit/Media/Infrastructure/FilesystemMediaStorageTest.php
    - tests/Unit/Navigation/Application/NavigationServiceTest.php
    - tests/Unit/Navigation/Application/PublicNavigationTest.php
    - tests/Unit/OpenApi/Application/OpenApiComponentClaimAdmissionTest.php
    - tests/Unit/OpenApi/Application/OpenApiExtensionActivationAdmissionTest.php
    - tests/Unit/OpenApi/Delivery/Http/OpenApiHandlerTest.php
    - tests/Unit/Portal/Application/MembershipPortalSessionIdentityLoaderTest.php
    - tests/Unit/Portal/Http/PortalDashboardPreferencesHandlerTest.php
    - tests/Unit/Portal/Http/PortalHomeHandlerTest.php
    - tests/Unit/Portal/Http/PortalSecurityBoundaryTest.php
    - tests/Unit/Portal/Http/PortalSecurityHandlerTest.php
    - tests/Unit/Portal/Infrastructure/Identity/DoctrinePortalPrincipalLoaderTest.php
    - tests/Unit/Portal/Presentation/PortalContributionRendererTest.php
    - tests/Unit/Presentation/ContentLayoutCatalogTest.php
    - tests/Unit/Presentation/Twig/IsolatedTwigEnvironmentFactoryTest.php
    - tests/Unit/Site/Application/PublicPageLocatorTest.php
    - tests/Unit/Site/Infrastructure/Persistence/DoctrineSiteSettingsTest.php
    - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringContextAuthorityTest.php
    - tests/Unit/Studio/Application/Authoring/ContentStudioAuthoringTargetResolverTest.php
    - tests/Unit/Studio/Application/Composition/StudioPublishedContentRendererTest.php
    - tests/Unit/Studio/Application/Composition/StudioPublishedThemeTest.php
    - tests/Unit/Studio/Application/Host/StudioArtifactHostPortTest.php
    - tests/Unit/Studio/Application/Host/StudioHostSessionAuthorityTest.php
    - tests/Unit/Studio/Application/Host/StudioProducerMutationBoundaryTest.php
    - tests/Unit/Studio/Application/Host/StudioProducerRequestAuthorityTest.php
    - tests/Unit/Studio/Application/Host/StudioResourceHostPortTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaAuditTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaLifecycleTest.php
    - tests/Unit/Studio/Application/Media/StudioMediaProducerPortTest.php
    - tests/Unit/Studio/Application/Preview/ContentStudioPreviewBindingSourceTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewActivityRecorderTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewBindingRendererTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewHostPortTest.php
    - tests/Unit/Studio/Application/Preview/StudioPreviewProducerPortTest.php
    - tests/Unit/Studio/Application/Projection/ContentStudioProjectorTest.php
    - tests/Unit/Studio/Application/Projection/ContentStudioResourceSearchProviderTest.php
    - tests/Unit/Studio/Application/Projection/RecordAuthorizedStudioContentFieldDisclosureTest.php
    - tests/Unit/Studio/Application/Projection/StudioContentProjectionServiceTest.php
    - tests/Unit/Studio/Domain/Projection/ContentBlueprintBindingTest.php
    - tests/Unit/Studio/Domain/Projection/EntryCompositionOverridesTest.php
    - tests/Unit/Studio/Infrastructure/Persistence/DoctrineContentProjectionBindingRepositoryTest.php
    - tests/Unit/Support/TransientBusinessDefinitionFixtureScopeTest.php
    - tests/Unit/Workflow/Domain/WorkflowTest.php
  di_or_provisioning_changes:
    - "No provider; supply canonical values explicitly and preserve App identity/SDK/request adapters."
  capability_index_changes:
    - "Generate the six access-context capabilities from the exact installed manifests."
  changelog_and_evidence_changes:
    - "Update NRM-2026-005, MIG004/CS004 evidence, immutable attestation and serialized integration train."
  verification_commands:
    - "composer qa"
    - "composer kumwe:capability-index-check"
    - "composer kumwe:core-growth-check"
    - "Supported App database, authorization, lifecycle, deployment and recovery suites."
concurrency:
  likely_conflict_files:
    - composer.json
    - composer.lock
    - src/Kernel/ContainerFactory.php
    - build/capability-index/v1.json
    - CHANGELOG.md
  related_migrations: []
  ownership_conflicts: []
  integration_train: null
  resolution_rule: semantic-preservation
governance:
  roadmap_source_sha256: a202155ef1a65f5ab293d4f8397ebf4ac430db7f1e877c776bbe7851e6fe18d8
  roadmap_refs: []
  non_roadmap_refs:
    - NRM-2026-005
  completion_claim: false
decisions:
  - "Preserve MIG004 from the existing extraction; this PR completes its unfinished package evidence."
  - "Principal/SystemActor/InvalidContext are new portable declarations, not copied App concrete identities."
  - "Eight mapped values plus three new declarations give eleven public symbols."
  - "Capabilities/grants stay outside this closure; no copied identity model or dependency expansion."
  - "StepUpProof rejects a workspace without an organization and documents host-only action checks."
  - "Reject raw scope control bytes before trim can erase them; retain valid identifier normalization."
blockers:
  - "Human merge, immutable publication and independent verification are future prerequisites."
---

## Migration/implementation summary

11 types; explicit principal/system ports, immutable scope values, proof/fingerprint/redaction semantics. Malformed
UTF-8 identity rejection is now package-owned alongside the existing grammar, bounds and proof suites.

## Public API and responsibility

The symbol map above and [public API](docs/public-api.md) define every exported contract.
[Architecture](docs/architecture.md) and [integration](docs/integration.md) retain the host boundaries.

## Capability reuse/semantic input review

No Kumwe runtime dependencies. Context 0.1.1 remains the latest published version; this review records 0.1.2 for
the next human-reviewed release. [Current release/dependency observations](docs/readiness-review.md) supersede
obsolete initial-extraction publication blockers. No independent attestation is fabricated.

## Consumer inventory

The source/consumer mappings above remain the adoption inventory. Compare every mapped file and public signature
against the recorded full App baseline and current App before consumer changes. Any newer portable behavior goes
upstream first. Preserve App authority, adapters and workflows.

## Test ownership

Package tests own portable behavior, boundary/conformance, API and construction. App retains actual authorization,
transaction atomicity, persistence, concurrency, recovery and delivery tests. Remove only duplicate portable
implementation tests during the separate verified adoption.

## Next-task execution notes

Review [PR #8](https://github.com/kumwe/access-context/pull/8), require its complete package gate, then let the
maintainer merge. Independently verify the published successor and exact dependency graph before App adoption.
Existing published releases stay intact. This task does not implement the App runtime cutover.

## Drift check

Reconcile mapped source and tests against the recorded App baseline and current App before any adoption.
Newer portable behavior must move upstream first; preserve App authority, persistence and integration tests.

## Validation recipe and observed local results

Run `composer check` and the repository release automation regressions. Runtime suites, strict static analysis,
coding standards, manifest/API checks and the no-dev authoritative archive consumer remain required. Final tested
source and archive identities belong in external CI/attestation evidence.
